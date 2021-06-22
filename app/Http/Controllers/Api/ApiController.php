<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateDoctorNameRequest;
use App\Models\AppointmentHistory;
use App\Models\DoctorService;
use App\Models\DoctorWorktime;

class ApiController extends Controller
{
    public function doctor(UpdateDoctorNameRequest $request, DoctorService $doctorService)
    {
        $doctorService->update(['doctor_name' => $request->name]);

        return response()->json(['status' => 'success']);
    }

    public function close(DoctorWorktime $doctorWorktime)
    {
        $hasConflict = AppointmentHistory::whereIn('status', ['Menunggu', 'Konflik'])
            ->whereDoctorWorktimeId($doctorWorktime->id)
            ->whereDate('date', '>', today())
            ->exists();

        if ($hasConflict) {
            \DB::transaction(function () use ($doctorWorktime) {
                $conflictId = DoctorWorktime::insertGetId([
                    'doctor_service_id' => $doctorWorktime->doctor_service_id,
                    'quota' => 0,
                    'day' => $doctorWorktime->day,
                    'time_start' => '00:00',
                    'time_end' => '00:00',
                    'active_date' => null
                ]);

                $doctorWorktime->update(['replaced_with_id' => $conflictId]);

                AppointmentHistory::whereDoctorWorktimeId($doctorWorktime->id)->update([
                    'status' => 'Konflik'
                ]);
            });

            $time = "$doctorWorktime->time_start - $doctorWorktime->time_end";
            return response()->json([
                'status' => 'warning',
                'message' => "Sudah ada pasien yang mendaftar pada jadwal ini\nJadwal asli: $time"
            ]);
        }

        $doctorWorktime->delete();
        return response()->json(['status' => 'success']);
    }
}
