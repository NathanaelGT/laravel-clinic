<?php

namespace App\Http\Controllers\Api;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreServiceRequest;
use App\Models\DoctorService;
use App\Models\DoctorWorktime;
use App\Models\Service;
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
}
