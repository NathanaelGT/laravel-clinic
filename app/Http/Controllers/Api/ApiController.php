<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateDoctorNameRequest;
use App\Http\Requests\Api\UpdateServiceRequest;
use App\Models\DoctorService;
use App\Models\DoctorWorktime;

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
            $newId = DoctorWorktime::insertGetId([
                'doctor_service_id' => $request->doctorServiceId,
                'day' => $request->day,
                'quota' => $request->quota,
                'time_start' => $request->timeStart,
                'time_end' => $request->timeEnd
            ]);
            return response()->json(['status' => 'success', 'newId' => $newId]);
        }

        DoctorWorktime::findOrFail($id)->update([
            'quota' => $request->quota,
            'time_start' => $request->timeStart,
            'time_end' => $request->timeEnd
        ]);
        return response()->json(['status' => 'success']);
    }

    public function close(DoctorWorktime $doctorWorktime)
    {
        $doctorWorktime->delete();
        return response()->json(['status' => 'success']);
    }
}
