<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class PatientRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $service = Service::whereName($request->input('layanan'))->firstOrFail();
        $doctors = $service->doctorService->all();

        $index = 0;
        $schedules = [];
        foreach ($doctors as $doctor) {
            $array = &$schedules[$index];
            if (!isset($array)) $array = [];

            foreach ($doctor->doctorWorktime as $schedule) {
                $time = $schedule['time_start'] . ' - ' . $schedule['time_end'];
                $day = &$schedules[$index][$schedule['day']];

                if (!isset($day)) $day = [$time];
                else array_push($day, $time);
            }
            $index++;
        }

        $service = $service['name'];
        $doctors = array_column($doctors, 'doctor_name');
        $formAction = route('patient-registration:store');
        $formMethod = 'POST';

        return view('registration', compact('service', 'doctors', 'schedules', 'formAction', 'formMethod'));
    }

    public function store(Request $request)
    {
        dd($request->all());
        return redirect(route('home'));
    }
}
