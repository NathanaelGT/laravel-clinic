<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorService;

class DoctorController extends Controller
{
    public function list()
    {
        $services = DoctorService::with('doctorWorktime', 'service')->get()->toArray();

        $ids = [];
        $data = array_reduce($services, function($carry, $item) use (&$ids) {
            $serviceName = $item['service']['name'];
            $array = &$carry[$serviceName];

            if (!isset($array)) $array = [];
            $ids["$serviceName.{$item['doctor_name']}"] = $item['id'];

            $array[$item['doctor_name']] = array_reduce($item['doctor_worktime'], function($carry, $item) {
                $array = &$carry[$item['day']];
                $timeAndQuota = [
                    'id' => $item['id'],
                    'time' => $item['time_start'] . ' - ' . $item['time_end'],
                    'quota' => $item['quota']
                ];

                if (!isset($array)) $array = [$timeAndQuota];
                else array_push($array, $timeAndQuota);

                return $carry;
            }, []);

            return $carry;
        }, []);

        return view('admin.doctor-list', compact('data', 'ids'));
    }

    public function delete(DoctorService $doctorService)
    {
        $doctorService->delete();
        return redirect()->route('admin@doctor-list');
    }
}
