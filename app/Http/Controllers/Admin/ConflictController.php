<?php

namespace App\Http\Controllers\Admin;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Models\AppointmentHistory;
use App\Models\Conflict;

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
