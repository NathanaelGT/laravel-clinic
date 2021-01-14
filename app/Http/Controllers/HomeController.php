<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $services = [
            ['name' => 'Gigi', 'doctor' => 'Budi'],
            ['name' => 'Ibu dan Anak', 'doctor' => 'Agus'],
            ['name' => 'Umum', 'doctor' => 'Dewi'],
            ['name' => 'Kulit', 'doctor' => 'Bima'],
            ['name' => 'Mata', 'doctor' => 'John'],
            ['name' => 'Penyakit Dalam', 'doctor' => 'Ibnu']
        ];

        $workingHours = [
            [
                'Senin' => ['08:00 - 11:30', '14:00 - 16:00'],
                'Selasa' => ['08:00 - 11:30', '14:00 - 16:00'],
                'Rabu' => ['08:00 - 11:30', '14:00 - 16:00'],
                'Kamis' => ['08:00 - 11:30', '14:00 - 16:00'],
                'Jumat' => ['08:00 - 11:00'],
                'Sabtu' => ['08:00 - 11:30', '14:00 - 15:00']
            ],
            [
                'Senin' => ['20:00 - 22:00'],
                'Rabu' => ['09:00 - 12:00', '20:00 - 22:00'],
                'Jumat' => ['08:00 - 10:30']
            ],
            [
                'Senin' => ['09:00 - 12:00', '15:00 - 18:00', '20:00 - 22:00'],
                'Selasa' => ['09:00 - 12:00', '15:00 - 18:00', '20:00 - 22:00'],
                'Rabu' => ['09:00 - 12:00', '15:00 - 18:00', '20:00 - 22:00'],
                'Kamis' => ['09:00 - 12:00', '15:00 - 18:00', '20:00 - 22:00'],
                'Jumat' => ['08:00 - 11:30']
            ],
            [
                'Senin' => ['13:30 - 18:00'],
                'Selasa' => ['13:00 - 18:00'],
                'Rabu' => ['13:00 - 18:00'],
                'Kamis' => ['13:00 - 18:00'],
                'Jumat' => ['14:00 - 17:00'],
                'Sabtu' => ['14:00 - 17:30']
            ],
            [
                'Senin' => ['16:00 - 17:30', '19:00-21:30'],
                'Selasa' => ['16:00 - 17:30', '19:00-21:30'],
                'Rabu' => ['16:00 - 17:30', '19:00-21:30'],
                'Kamis' => ['16:00 - 17:30', '19:00-21:30'],
                'Jumat' => ['16:00 - 17:30', '19:00-21:30'],
                'Sabtu' => ['16:00 - 17:30', '19:00-21:30']
            ],
            [
                'Senin' => ['09:00 - 12:00'],
                'Selasa' => ['09:00 - 12:00'],
                'Rabu' => ['09:00 - 12:00', '13:00 - 18:00', '19:00 - 21:00'],
                'Kamis' => ['09:00 - 12:00', '13:00 - 18:00', '19:00 - 21:00']
            ],
        ];

        $services = array_chunk($services, ceil(sizeof($services) / 3));

        if (sizeof($services[1]) - sizeof($services[2]) === 2) {
            array_push($services[2], array_pop($services[1]));
        }

        return view('home', compact('services', 'workingHours'));
    }
}
