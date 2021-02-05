<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateDoctorNameRequest;
use App\Http\Requests\Api\UpdateServiceRequest;
use App\Models\Conflict;
use App\Models\DoctorService;
use App\Models\DoctorWorktime;
use App\Models\ServiceAppointment;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function doctor(UpdateDoctorNameRequest $request, DoctorService $doctorService)
    {
        $doctorService->update(['doctor_name' => $request->name]);

        return response()->json(['status' => 'success']);
    }

    public function service(UpdateServiceRequest $request, $id)
    {
        if ($id === 'new') {
            $newId = 100;DoctorWorktime::insertGetId([
                'doctor_service_id' => $request->doctorServiceId,
                'day' => $request->day,
                'quota' => $request->quota,
                'time_start' => $request->timeStart,
                'time_end' => $request->timeEnd
            ]);
            return response()->json(['status' => 'success', 'newId' => $newId]);
        }
        $worktime = DoctorWorktime::findOrFail($id);
        $appointmentId = ServiceAppointment::whereDoctorWorktimeId($worktime->id)
            ->where('date', '>', Carbon::today())
            ->first()
            ?->id;

        if (!$appointmentId) {
            $worktime->update([
                'quota' => $request->quota,
                'time_start' => $request->timeStart,
                'time_end' => $request->timeEnd
            ]);
            return response()->json(['status' => 'success']);
        }

        Conflict::updateOrCreate([
            'service_appointment_id' => $appointmentId,
            'doctor_worktime_id' => $worktime->id,
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

    public function close(DoctorWorktime $doctorWorktime)
    {
        $doctorWorktime->delete();
        return response()->json(['status' => 'success']);
    }
}
