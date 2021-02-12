<?php

namespace App\Http\Controllers\Api;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MergeServiceRequest;
use App\Http\Requests\Api\StoreServiceRequest;
use App\Http\Requests\Api\UpdateServiceRequest;
use App\Models\Conflict;
use App\Models\DoctorService;
use App\Models\DoctorWorktime;
use App\Models\Service;
use App\Models\ServiceAppointment;
use Carbon\Carbon;
use Closure;
use Exception;

class ServiceController extends Controller
{
    public function store(StoreServiceRequest $request)
    {
        if (sizeof($request->quota) !== sizeof($request->day))
            throw new Exception('Invalid quota and day data', 400);

        foreach($request->time as $index => $time) {
            $minutes = Helpers::timeToNumber($time[1]) - Helpers::timeToNumber($time[0]);
            if ($minutes % $request->quota[$index])
                throw new Exception('Invalid time and/or quota data', 400);
        }

        \DB::transaction(function() use ($request) {
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

        // SECTION buat baru
        if ($id === 'new') {
            $newId = DoctorWorktime::insertGetId([
                'doctor_service_id' => $request->doctorServiceId,
                'day' => $request->day,
                'quota' => $quota,
                'time_start' => $request->timeStart,
                'time_end' => $request->timeEnd
            ]);
            return response()->json(['status' => 'success', 'newId' => $newId]);
        }


        $appointment = ServiceAppointment::with('doctorWorktime')
            ->whereDoctorWorktimeId($id)
            ->where('date', '>', Carbon::today())
            ->first();

        // SECTION kalo quotanya masih belum ada yang booking/belum terdaftar
        $doctorWorktime = null;
        if (!$appointment) {
            $doctorWorktime = DoctorWorktime::find($id);

            if (!$doctorWorktime) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak dapat ditemukan'
                ]);
            }
        }
        elseif ($this->quotaIsEmpty($appointment->quota)) {
            $doctorWorktime = $appointment->doctorWorktime;
        }
        if ($doctorWorktime) {
            $activeDate = Carbon::parse($doctorWorktime->active_date)->minutes(0);
            $twins = null;
            if ($activeDate->isFuture()) {
                $twins = DoctorWorktime::whereDoctorServiceId($doctorWorktime->doctor_service_id)
                    ->whereQuota($quota)
                    ->where('day', $doctorWorktime->day)
                    ->whereTimeStart($request->timeStart)
                    ->whereTimeEnd($request->timeEnd)
                    ->whereDate('deleted_at', $activeDate)
                    ->first();
            }
            $doctorWorktime->update([
                'quota' => $quota,
                'time_start' => $request->timeStart,
                'time_end' => $request->timeEnd
            ]);

            $message = ['status' => 'success'];
            if ($twins) $message['twinsId'] = $twins->id;
            return response()->json($message);
        }


        // SECTION quota yang tersimpan dan yang baru sama, yang beda waktunya
        $doctorWorktime = $appointment->doctorWorktime;
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
            $success = false;
            if (
                ($oldStart > $newStart && $newEnd === $oldEnd) ||
                ($newEnd > $oldEnd && $oldStart === $newStart) ||
                ($oldStart > $newStart && $newEnd > $oldEnd)
            ) {
                $success = true;
                if ($oldStart > $newStart) {
                    $this->addSlot($oldStart, $newStart, $appointment, function ($newQuota, $oldQuota) {
                        return array_merge($newQuota, $oldQuota);
                    });
                }
                if ($newEnd > $oldEnd) {
                    $this->addSlot($newEnd, $oldEnd, $appointment, function ($newQuota, $oldQuota) {
                        return array_merge($oldQuota, $newQuota);
                    });
                }
            }


            // SECTION antara waktu mulainya lebih lama, waktu selesainya lebih cepat, atau dua duanya
            else {
                $startDifference = $newStart - $oldStart;
                $endDifference = $oldEnd - $newEnd;
                $startSlotCount = $startDifference / $request->quota;
                $endSlotCount = $endDifference / $request->quota;

                $startQuota = $startSlotCount ? array_slice($appointment->quota, 0, $startSlotCount) : [];
                $endQuota = $endSlotCount ? array_slice($appointment->quota, -$endSlotCount) : [];


                // SECTION quota yang bakal "dihilangin" belum ada yang booking
                if ($this->quotaIsEmpty($startQuota) && $this->quotaIsEmpty($endQuota)) {
                    $success = true;
                    $appointment->quota = array_slice(
                        $appointment->quota,
                        $startSlotCount,
                        -$endSlotCount ?: sizeof($appointment->quota)
                    );
                }
            }

            if ($success) {
                $appointment->doctorWorktime->time_start = $request->timeStart;
                $appointment->doctorWorktime->time_end = $request->timeEnd;
                $appointment->push();

                return response()->json(['status' => 'success']);
            }
        }


        // SECTION antara sudah ada yang booking atau quota beda
        Conflict::updateOrCreate([
            'service_appointment_id' => $appointment->id,
            'doctor_worktime_id' => $appointment->doctorWorktime->id,
        ], [
            'quota' => $request->quota,
            'time_start' => $request->timeStart,
            'time_end' => $request->timeEnd
        ]);

        return response()->json([
            'status' => 'warning',
            'message' => 'Sudah ada pasien yang mendaftar pada jadwal ini'
        ]);
    }

    public function merge(MergeServiceRequest $request)
    {
        $id = $request->validated();
        sort($id);
        try {
            $first = DoctorWorktime::find($id[0]);
            $second = DoctorWorktime::whereId($id[1])
                ->doesnthave('serviceAppointment')
                ->doesnthave('conflict')
                ->first();
        }
        catch(Exception $ignored) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak dapat ditemukan',
            ]);
        }

        if (sizeof(array_diff_assoc($first->toArray(), $second->toArray())) > 3) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang diberikan tidak cocok',
            ]);
        }


        \DB::transaction(function() use ($first, $second) {
            $first->restore();
            $second->delete();
        });

        return response()->json(['status' => 'success', 'deleted_id' => $second->id]);
    }


    private function quotaIsEmpty(array $quota, bool $checkEmptied = false)
    {
        foreach ($quota as $slot) {
            if ((int) $slot !== 0 || ($checkEmptied && (int) $slot !== -1)) {
                return false;
            }
        }

        return true;
    }

    private function addSlot(int $first, int $second, $appointment, Closure $closure)
    {
        $extraTime = $first - $second;
        $extraQuota = $extraTime / $appointment->doctorWorktime->quota;
        $newQuota = [];

        for ($i = 0; $i < $extraQuota; $i++) $newQuota[$i] = 0;

        $appointment->quota = $closure($newQuota, $appointment->quota);
    }
}
