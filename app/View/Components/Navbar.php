<?php

namespace App\View\Components;

use App\Models\DoctorWorktime;
use Illuminate\View\Component;

class Navbar extends Component
{
    private $navigations = [];

    public function __construct()
    {
        if (DoctorWorktime::hasConflict()) {
            $this->navigations[] = ['route' => route('admin@conflict'), 'name' => 'Jadwal yang "bermasalah"'];
        }

        $this->navigations[] = ['route' => route('admin@log'), 'name' => 'Daftar Kunjungan'];
        $this->navigations[] = ['route' => route('admin@patient-list'), 'name' => 'Daftar Pasien'];
        $this->navigations[] = ['route' => route('admin@doctor-list'), 'name' => 'Daftar Dokter'];
        $this->navigations[] = ['route' => route('logout'), 'name' => 'Keluar'];
    }

    public function render()
    {
        return view('components.navbar', get_object_vars($this));
    }
}
