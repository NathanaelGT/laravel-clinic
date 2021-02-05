<?php

namespace App\Http\Controllers;

use App\Models\DoctorService;

class HomeController extends Controller
{
    public function index()
    {
        $services = DoctorService::with('doctorWorktime', 'service')->get()->toArray();

        $data = array_reduce($services, function($carry, $item) {
            $serviceName = $item['service']['name'];
            $array = &$carry[$serviceName];

            if (!isset($array)) $array = [];

            $array[$item['doctor_name']] = array_reduce($item['doctor_worktime'], function($carry, $item) {
                $array = &$carry[$item['day']];
                $time = $item['time_start'] . ' - ' . $item['time_end'];

                if (!isset($array)) $array = $time;
                else $array .= ', ' . $time;

                return $carry;
            }, []);

            return $carry;
        }, []);

        return view('home', compact('data'));
    }
}
