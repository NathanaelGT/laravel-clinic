<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public $dummy;

    public function __construct()
    {
        Carbon::setLocale('id');
        $this->dummy = [
            (object) [
                'id' => 1,
                'name' => 'Andi',
                'nik' => '21312873421',
                'phone_number' => '085858585858',
                'address' => 'Jl. jalan bareng',
                'status' => config('global.status')[1],
                'service' => config('global.services')[4],
                'time_start' => Carbon::create(2021, 1, 18, 16, 00, 0, '+08:00'),
                'doctor' => config('global.doctors')[4][0],
                'service_per' => 45
            ],
            (object) [
                'id' => 2,
                'name' => 'Otto',
                'nik' => '21312873422',
                'phone_number' => '085800008580',
                'address' => 'Jl. sana sini',
                'status' => config('global.status')[0],
                'service' => config('global.services')[0],
                'time_start' => Carbon::create(2021, 1, 18, 15, 00, 0, '+08:00'),
                'doctor' => config('global.doctors')[0][0],
                'service_per' => 30
            ],
            (object) [
                'id' => 3,
                'name' => 'Thornton',
                'nik' => '21312873423',
                'phone_number' => '087654456780',
                'address' => 'Jl. ini',
                'status' => config('global.status')[0],
                'service' => config('global.services')[0],
                'time_start' => Carbon::create(2021, 1, 20, 10, 30, 0, '+08:00'),
                'doctor' => config('global.doctors')[0][1],
                'service_per' => 30
            ],
            (object) [
                'id' => 4,
                'name' => 'Ray',
                'nik' => '21312873424',
                'address' => 'Jl. itu',
                'phone_number' => '087654321000',
                'status' => config('global.status')[0],
                'service' => config('global.services')[2],
                'time_start' => Carbon::create(2021, 1, 28, 16, 20, 0, '+08:00'),
                'doctor' => config('global.doctors')[2][0],
                'service_per' => 20
            ]
        ];
    }

    public function list()
    {
        $patients = [];
        foreach ($this->dummy as $patient) {
            if ($patient->status === config('global.status')[0])
                array_push($patients, $patient);
        }
        //WHERE status = `Menunggu` ORDER BY `time_start`

        return view('admin.patient-list', compact('patients'));
    }

    public function reschedule(Request $request)
    {
        foreach ($this->dummy as $patient) {
            if ($patient->id == $request->id && $patient->status !== 'Selesai') {
                $services = config('global.services');
                $service = $patient->service;
                $index = array_search($service, $services);

                $doctors = config('global.doctors')[$index];
                $workingSchedules = config('global.workingSchedules')[$index];
                $formAction = route('admin@patient-reschedule:put');
                $formMethod = 'PUT';

                $index = array_search($patient->doctor, $doctors);
                $availableDays = array_keys($workingSchedules[$index]);

                return view(
                    'registration',
                    compact(
                        'service', 'doctors', 'workingSchedules', 'formAction',
                        'formMethod', 'patient', 'availableDays'
                    )
                );
            }
        }

        return abort(404);
    }

    public function update(Request $request, $id)
    {
        return redirect(route('admin@patient-list'));
    }
}
