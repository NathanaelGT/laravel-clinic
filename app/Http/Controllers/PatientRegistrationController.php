<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $services = config('global.services');
        $service = $request->input('layanan');
        $index = array_search($service, $services);

        if (!is_numeric($index)) abort(404);

        $doctors = config('global.doctors')[$index];
        $workingSchedules = config('global.workingSchedules')[$index];
        $formAction = route('patient-registration:store');
        $formMethod = 'post';

        return view('registration', compact('service', 'doctors', 'workingSchedules', 'formAction', 'formMethod'));
    }

    public function store(Request $request)
    {
        return redirect(route('home'));
    }
}
