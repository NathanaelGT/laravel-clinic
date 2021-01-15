<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $services = config('global.services');
        $doctors = config('global.doctors');
        $workingSchedules = config('global.workingSchedules');

        $services = array_chunk($services, ceil(sizeof($services) / 3));

        if (sizeof($services[1]) - sizeof($services[2]) === 2) {
            array_push($services[2], array_pop($services[1]));
        }

        return view('home', compact('services', 'doctors', 'workingSchedules'));
    }
}
