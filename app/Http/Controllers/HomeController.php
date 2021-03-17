<?php

namespace App\Http\Controllers;

use App\Models\DoctorService;
use App\Models\DoctorWorktime;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::with([
            'doctorService' => function ($query) {
                $query->orderBy('display_order');
            },
            'doctorService.doctorWorktime'
        ])
            ->has('doctorService')
            ->orderBy('display_order')
            ->get();

        $draft = [];
        $data = $services->reduce(function ($carry, Service $service) use (&$draft) {
            $serviceName = $service->name;
            $array = &$carry[$serviceName];

            $array = [];

            $array = $service->doctorService->reduce(
                function ($carry, DoctorService $doctorService) use (&$draft) {
                    $carry[$doctorService['doctor_name']] = $doctorService->doctorWorktime->reduce(
                        function ($carry, DoctorWorktime $doctorWorktime) use (&$draft) {
                            if (isset($draft[$doctorWorktime->id])) {
                                return $carry;
                            }

                            if ($doctorWorktime->replaced_with_id) {
                                $draft[$doctorWorktime->replaced_with_id] = true;
                            }

                            $array = &$carry[$doctorWorktime->day];
                            $time = $doctorWorktime->time_start . ' - ' . $doctorWorktime->time_end;

                            if (isset($array)) $array .= ', ' . $time;
                            else $array = $time;
                            return $carry;
                        }
                    );
                    return $carry;
                }
            );
            return $carry;
        }, []);

        return view('home', compact('data'));
    }
}
