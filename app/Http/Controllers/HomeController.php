<?php

namespace App\Http\Controllers;

use App\Models\DoctorService;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::with([
            'doctorService' => fn ($query) => $query->orderBy('display_order'),
            'doctorService.doctorWorktime'
        ])
            ->orderBy('display_order')
            ->get()
            ->toArray();

        $data = array_reduce($services, function($carry, $service) {
            $serviceName = $service['name'];
            $array = &$carry[$serviceName];

            $array = [];

            $array = array_reduce(
                $service['doctor_service'],
                function($carry, $doctorService) {
                    $carry[$doctorService['doctor_name']] = array_reduce(
                        $doctorService['doctor_worktime'],
                        function ($carry, $schedule) {
                            $array = &$carry[$schedule['day']];
                            $time = $schedule['time_start'] . ' - ' . $schedule['time_end'];

                            if (isset($array)) $array .= ', ' . $time;
                            else $array = $time;
                            return $carry;
                        }
                    );
                    return $carry;
                }
            );
            return $carry;
        });

        return view('home', compact('data'));
    }
}
