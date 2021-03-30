<?php

namespace App\Http\Controllers\Api;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreServiceRequest;
use App\Http\Requests\Api\UpdateServiceRequest;
use App\Models\AppointmentHistory;
use App\Models\DoctorService;
use App\Models\DoctorWorktime;
use App\Models\Service;
use Exception;

class ServiceController extends Controller
{
    public function store(StoreServiceRequest $request)
    {
        if (sizeof($request->quota) !== sizeof($request->day)) {
            throw new Exception('Invalid quota and day data', 400);
        }

        foreach($request->time as $index => $time) {
            $minutes = Helpers::timeToNumber($time[1]) - Helpers::timeToNumber($time[0]);
            if ($minutes % $request->quota[$index]) {
                throw new Exception('Invalid time and/or quota data', 400);
            }
        }

        \DB::transaction(function () use ($request) {
            $serviceName = ucwords(strtolower($request->serviceName));
            $serviceId = Service::firstOrCreate(['name' => $serviceName])->id;

            $doctorServiceId = DoctorService::insertGetId([
                'doctor_name' => $request->doctorName,
                'service_id' => $serviceId
            ]);

            foreach ($request->day as $index => $days) {
                [$timeStart, $timeEnd] = $request->time[$index];
                foreach ($days as $day) {
                    DoctorWorktime::create([
                        'doctor_service_id' => $doctorServiceId,
                        'quota' => $request->quota[$index],
                        'day' => $day,
                        'time_start' => $timeStart,
                        'time_end' => $timeEnd
                    ]);
                }
            }
        });
        return response()->json([
            'status' => 'success',
            'redirect' => route('admin@doctor-list')
        ]);
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        $quota = $request->quota;
        $start = Helpers::timeToNumber($request->timeStart);
        $end = Helpers::timeToNumber($request->timeEnd);

        $invalidMessage = null;
        if ($start >= $end) {
            $invalidMessage = 'Waktu mulai tidak bisa lebih besar atau sama dengan waktu selesai';
        }
        elseif (($end - $start) % $quota) {
            $invalidMessage = 'Waktu slot tidak dapat dibagi habis';
        }
        if ($invalidMessage) {
            return response()->json(['status' => 'error', 'message' => $invalidMessage]);
        }

        if ($id === 'new') {
            $newId = DoctorWorktime::insertGetId([
                'doctor_service_id' => $request->doctorServiceId,
                'quota' => $quota,
                'day' => $request->day,
                'time_start' => $request->timeStart,
                'time_end' => $request->timeEnd
            ]);
            return response()->json(['status' => 'success', 'newId' => $newId]);
        }

        $doctorWorktime = DoctorWorktime::find($id);
        if (!$doctorWorktime) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak dapat ditemukan'
            ]);
        }

        $appointments = AppointmentHistory::whereDoctorWorktimeId($doctorWorktime->id)->get();
        $storedConflicts = $appointments->where('status', 'Konflik');

        if ($appointments->isNotEmpty()) {
            // SECTION cari jadwal pasien yang bentrok
            $conflicts = $appointments->whereIn('status', ['Menunggu', 'Konflik'])
                ->where('date', '>', today())
                ->filter(function (AppointmentHistory $appointment) use ($start, $end) {
                    $appointmentStart = Helpers::timeToNumber($appointment->time_start);
                    $appointmentEnd = Helpers::timeToNumber($appointment->time_end);

                    return $start > $appointmentStart || $end < $appointmentEnd;
                });

            $newRecordId = null;
            $notConflict = null;
            $hasConflict = false;
            \DB::transaction(function () use (
                $end,
                $start,
                $request,
                $conflicts,
                $notConflict,
                $appointments,
                $doctorWorktime,
                $storedConflicts,
                &$hasConflict,
                &$newRecordId,
            ) {
                $now = now();

                $newRecordId = DoctorWorktime::insertGetId([
                    'doctor_service_id' => $doctorWorktime->doctor_service_id,
                    'quota' => $request->quota,
                    'day' => $doctorWorktime->day,
                    'time_start' => $request->timeStart,
                    'time_end' => $request->timeEnd,
                    'active_date' => $now
                ]);

                $doctorWorktime->update([
                    'replaced_with_id' => $newRecordId,
                    'deleted_at' => $now
                ]);

                // SECTION simpan id jadwal pasien yang bentrok
                AppointmentHistory::whereIn('id', $appointments->pluck('id'))
                    ->update(['doctor_worktime_id' => $newRecordId]);

                if ($conflicts->isNotEmpty()) {
                    $exist = $storedConflicts
                        ->whereIn('id', $conflicts->pluck('id'))
                        ->pluck('id');

                    $conflicts = $conflicts
                        ->whereNotIn('id', $exist)
                        ->pluck('id');

                    if ($conflicts) {
                        $hasConflict = true;
                        AppointmentHistory::whereIn('id', $conflicts)->update(['status' => 'Konflik']);
                    }
                }

                // SECTION kalo sudah engga "masalah" hapus conflictnya
                $notConflict = $storedConflicts
                    ->filter(function (AppointmentHistory $appointment) use ($start, $end) {
                        $appointmentStart = Helpers::timeToNumber($appointment->time_start);
                        $appointmentEnd = Helpers::timeToNumber($appointment->time_end);

                        return (
                            $start <= $appointmentStart && $start <= $appointmentEnd &&
                            $end >= $appointmentEnd && $end >= $appointmentStart
                        );
                    })
                    ->pluck('id');

                if ($notConflict->isNotEmpty()) {
                    if (sizeof($storedConflicts) === sizeof($notConflict)) {
                        $hasConflict = false;
                    }
                    AppointmentHistory::whereIn('id', $notConflict)->update([
                        'status' => 'Menunggu'
                    ]);
                }
            });

            if ($hasConflict) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Sudah ada pasien yang mendaftar pada jadwal ini',
                    'newId' => $newRecordId
                ]);
            }

            return response()->json(['status' => 'success', 'newId' => $newRecordId]);
        }

        $doctorWorktime->update([
            'quota' => $request->quota,
            'time_start' => $request->timeStart,
            'time_end' => $request->timeEnd,
        ]);
        return response()->json(['status' => 'success']);
    }
}
