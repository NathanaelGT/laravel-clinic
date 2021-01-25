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

        $index = 0;
        foreach($request->time as $time) {
            $minutes = Helpers::timeToNumber($time[1]) - Helpers::timeToNumber($time[0]);
            if ($minutes % $request->quota[$index++])
                throw new Exception('Invalid time and/or quota data', 400);
        }

        $serviceId = null;
        $doctorServiceId = null;
        $doctorWorktimeId = [];

        try {
            $serviceName = ucwords(strtolower($request->serviceName));
            $serviceId = Service::firstOrCreate(['name' => $serviceName])->id;

            $doctorServiceId = DoctorService::insertGetId([
                'doctor_name' => $request->doctorName,
                'service_id' => $serviceId
            ]);

            $index = 0;
            foreach ($request->day as $days) {
                [$timeStart, $timeEnd] = $request->time[$index];
                foreach ($days as $day) {
                    array_push(
                        $doctorWorktimeId,
                        DoctorWorktime::insertGetId([
                            'doctor_service_id' => $doctorServiceId,
                            'quota' => $request->quota[$index],
                            'day' => $day,
                            'time_start' => $timeStart,
                            'time_end' => $timeEnd
                        ])
                    );
                }
                $index++;
            }

            $message = [
                'status' => 'success',
                'redirect' => route('admin@doctor-list')
            ];

            return response()->json($message);
        }
        catch(Exception $exception) {
            if ($serviceId) Service::find($serviceId)->forceDelete();
            if ($doctorServiceId) DoctorService::find($serviceId)->forceDelete();
            foreach($doctorWorktimeId as $id) DoctorWorktime::find($id)->forceDelete();

            $message = [
                'status' => 'error',
                'message' => $exception->getMessage()
            ];
            if (env('APP_DEBUG')) array_push($message, ['stacktrace' => $exception->getTrace()]);

            return response()->json($message, 500);
        }
    }
}
