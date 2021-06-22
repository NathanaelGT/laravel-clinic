<?php

namespace App\View\Components;

use App\Conflict;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Navbar extends Component
{
    private $navigations = [];

    public function __construct()
    {
        if (Auth::check() && Conflict::any()) {
            $this->addNavigation('admin@conflict', 'Kunjungan yang bermasalah');
        }

        $this->addNavigation('admin@log', 'Daftar Semua Kunjungan');
        $this->addNavigation('admin@patient-list', 'Daftar Kunjungan');
        $this->addNavigation('admin@doctor-list', 'Daftar Dokter');
        $this->addNavigation('logout', 'Keluar');
    }

    public function render()
    {
        return view('components.navbar', get_object_vars($this));
    }

    private function addNavigation(string $route, string $text)
    {
        $this->navigations[] = ['route' => route($route), 'name' => $text];
    }
}
