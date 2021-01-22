<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function list()
    {
        $services = config('global.services');
        $doctors = config('global.doctors');
        $schedules = config('global.workingSchedules');
        $slot = config('global.schedules_per');

        return view('admin.doctor-list', compact('services', 'doctors', 'schedules', 'slot'));
    }
}
