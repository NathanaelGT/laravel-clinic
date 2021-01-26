<?php

namespace App\Http\Controllers;

use App\Models\DoctorService;
use App\Models\DoctorWorktime;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $schedule = DoctorWorktime::with('doctorService.service')->get()->toArray();

        $data = array_reduce($schedule, function($carry, $item) {
            $serviceName = $item['doctor_service']['service']['name'];
            $time = $item['time_start'] . ' - ' . $item['time_end'];

            $array = &$carry[$serviceName];
            $doctor = &$array[$item['doctor_service']['doctor_name']];
            $day = &$doctor[$item['day']];

            if (!isset($array)) $array = [];
            if (!isset($doctor)) $doctor = [];

            if (!isset($day)) $day = $time;
            else $day .= ', ' . $time;

            return $carry;
        }, []);

        return view('home', compact('data'));
    }
}
