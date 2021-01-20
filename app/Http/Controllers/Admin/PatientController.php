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
                'status' => config('global.status')[1],
                'service' => config('global.services')[4],
                'time_start' => Carbon::create(2021, 1, 18, 16, 00, 0, '+08:00'),
                'doctor' => config('global.doctors')[4][0],
                'service_per' => 45
            ],
            (object) [
                'id' => 2,
                'name' => 'Otto',
                'status' => config('global.status')[0],
                'service' => config('global.services')[0],
                'time_start' => Carbon::create(2021, 1, 18, 15, 00, 0, '+08:00'),
                'doctor' => config('global.doctors')[0][0],
                'service_per' => 30
            ],
            (object) [
                'id' => 3,
                'name' => 'Thornton',
                'status' => config('global.status')[0],
                'service' => config('global.services')[0],
                'time_start' => Carbon::create(2021, 1, 20, 10, 30, 0, '+08:00'),
                'doctor' => config('global.doctors')[0][1],
                'service_per' => 30
            ],
            (object) [
                'id' => 4,
                'name' => 'Ray',
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
}
