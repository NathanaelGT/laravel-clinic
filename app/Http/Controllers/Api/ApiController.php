<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateDoctorNameRequest;
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
        $doctorWorktime->delete();
        return response()->json(['status' => 'success']);
    }
}
