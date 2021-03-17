<?php

namespace App\Http\Controllers\Api;

use \Illuminate\Database\Eloquent\Collection;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MergeServiceRequest;
use App\Http\Requests\Api\StoreServiceRequest;
use App\Http\Requests\Api\UpdateServiceRequest;
use App\Models\AppointmentHistory;
use App\Models\DoctorService;
use App\Models\DoctorWorktime;
use App\Models\Service;
use Carbon\Carbon;
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
            $doctorServiceId = $request->doctorServiceId;
            $day = $request->day;
        }
        else {
            $doctorWorktime = DoctorWorktime::find($id);
            $doctorServiceId = $doctorWorktime->doctor_service_id;
            $day = $doctorWorktime->day;
        }

        // FIXME tambahin where id != draft
        $conflict = DoctorWorktime::whereDoctorServiceId($doctorServiceId)
            ->where('id', '!=', $id)
            ->where('day', $day)
            ->where(function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('time_start', '>=', $request->timeStart);
                    $query->where('time_start', '<=', $request->timeEnd);
                });

                $query->where(function ($query) use ($request) {
                    $query->where('time_end', '>=', $request->timeStart);
                    $query->where('time_end', '<=', $request->timeEnd);
                });
            })
            ->first();

        if ($conflict && $conflict->id !== $doctorWorktime?->replaced_with_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal ini berbentrokan dengan jadwal lain'
            ]);
        }

        // SECTION buat baru
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

        $appointment = AppointmentHistory::with([
            'doctorWorktime' => function ($query) {
                $query->orderBy('time_start');
            },
            'doctorWorktime.replacedWith'
        ])
            ->whereStatus('Menunggu')
            ->whereDoctorWorktimeId($id)
            ->whereDate('date', '>', today())
            ->get();

        // SECTION kalo quotanya masih penuh
        if ($appointment->isEmpty()) {
            $doctorWorktime = DoctorWorktime::find($id);

            if (!$doctorWorktime) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak dapat ditemukan'
                ]);
            }

            $doctorWorktime->update([
                'quota' => $quota,
                'time_start' => $request->timeStart,
                'time_end' => $request->timeEnd,
                'replaced_with_id' => null
            ]);

            $doctorWorktime?->replacedWith?->delete();

            return response()->json(['status' => 'success']);
        }


        // SECTION quota yang tersimpan dan yang baru sama, yang beda waktunya
        // diambil yang pertama, karna pasti sama semua doctorWorktimenya
        $doctorWorktime = $appointment[0]->doctorWorktime;
        $conflict = $doctorWorktime->replacedWith;
        if ($conflict) {
            if (
                !($request->skipPending ?? false) &&
                $quota === $doctorWorktime->quota &&
                $request->timeStart === $doctorWorktime->time_start &&
                $request->timeEnd === $doctorWorktime->time_end
            ) {
                return response()->json([
                    'status' => 'pending',
                    'info' => 'equal'
                ]);
            }

            $conflict->update([
                'quota' => $quota,
                'time_start' => $request->timeStart,
                'time_end' => $request->timeEnd
            ]);

            if ($request->skipPending ?? false) {
                return response()->json([
                    'status' => 'success',
                    'time' => "$request->timeStart - $request->timeEnd",
                    'quota' => $quota
                ]);
            }
            return response()->json(['status' => 'success']);
        }

        if ($quota === $doctorWorktime->quota) {
            $oldStart = Helpers::timeToNumber($doctorWorktime->time_start);
            $newStart = Helpers::timeToNumber($request->timeStart);
            $oldEnd = Helpers::timeToNumber($doctorWorktime->time_end);
            $newEnd = Helpers::timeToNumber($request->timeEnd);

            if ($newEnd === $oldEnd && $oldStart === $newStart) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Tidak ada yang berubah'
                ]);
            }


            // SECTION antara waktu mulai lebih cepat, waktu selesai lebih lama, atau dua duanya
            $isValid = false;
            if (
                ($oldStart > $newStart && $newEnd === $oldEnd) ||
                ($newEnd > $oldEnd && $oldStart === $newStart) ||
                ($oldStart > $newStart && $newEnd > $oldEnd)
            ) $isValid = true;

            // SECTION antara waktu mulainya lebih lama, waktu selesainya lebih cepat, atau dua duanya
            else {
                [$firstPatientHour, $lastPatientHour] = $this->getLowestAndHighestHour($appointment);

                // SECTION quota yang bakal "dihilangin" belum ada yang booking
                if ($newStart < $firstPatientHour && $newEnd > ($lastPatientHour + $quota)) {
                    $isValid = true;
                }
            }

            if ($isValid) {
                $doctorWorktime->update([
                    'time_start' => $request->timeStart,
                    'time_end' => $request->timeEnd,
                    'replaced_with_id' => null
                ]);

                $doctorWorktime?->replacedWith?->delete();

                return response()->json(['status' => 'success']);
            }
        }


        // SECTION antara sudah ada yang booking atau quota beda
        \DB::transaction(function () use ($doctorWorktime, $request) {
            $conflictId = DoctorWorktime::insertGetId([
                'doctor_service_id' => $doctorWorktime->doctor_service_id,
                'quota' => $request->quota,
                'day' => $doctorWorktime->day,
                'time_start' => $request->timeStart,
                'time_end' => $request->timeEnd,
                'active_date' => null
            ]);

            $doctorWorktime?->replacedWith?->delete();
            $doctorWorktime->update(['replaced_with_id' => $conflictId]);
        });

        $time = "$doctorWorktime->time_start - $doctorWorktime->time_end";
        return response()->json([
            'status' => 'warning',
            'message' => "Sudah ada pasien yang mendaftar pada jadwal ini\nJadwal asli: $time"
        ]);
    }

    public function destroyConflict(DoctorWorktime $doctorWorktime)
    {
        // biar di dalam closure tetap bisa akses
        $doctorWorktime->load('replacedWith');

        \DB::transaction(function () use ($doctorWorktime) {
            $doctorWorktime->update(['replaced_with_id' => null]);
            $doctorWorktime->replacedWith->update([
                'replaced_with_id' => $doctorWorktime->id,
                'deleted_at' => Carbon::now()
            ]);
        });

        return response()->json([
            'status' => 'success',
            'info' => "$doctorWorktime->time_start - $doctorWorktime->time_end"
        ]);
    }


    private function getLowestAndHighestHour(Collection $appointmentHistory) {
        $result = $appointmentHistory->map(fn ($appointment) => (
            Helpers::timeToNumber($appointment->time_start)
        ));

        return [$result->min(), $result->max()];
    }
}
