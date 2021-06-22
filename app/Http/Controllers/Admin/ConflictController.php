<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentHistory;

class ConflictController extends Controller
{
    public function list()
    {
        $appointmentHistories = AppointmentHistory::whereStatus('Konflik')
            ->orderBy('date')
            ->orderBy('time_start')
            ->get();

        return view('admin.conflict-list', compact('appointmentHistories'));
    }
}
