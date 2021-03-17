<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorWorktime;
use Carbon\Carbon;

class ConflictController extends Controller
{
    public function list()
    {
        $doctorWorktimes = DoctorWorktime::whereNotNull('replaced_with_id')
            ->whereNull('deleted_at')
            ->with([
                'replacedWith' => function ($query) {
                    $query->select(['id', 'time_start', 'time_end', 'quota']);
                },
                'doctorService' => function ($query) {
                    $query->select(['id', 'doctor_name']);
                },
                'appointmentHistory' => function ($query) {
                    $query->whereStatus('Menunggu');
                    $query->orderBy('time_start');
                    $query->select(['id', 'date', 'doctor_worktime_id', 'patient_name']);
                }
            ])
            ->withTrashed() //biar conditionnya engga kedouble
            ->get();

        return view('admin.conflict-list', compact('doctorWorktimes'));
    }

    public function nextWeek(DoctorWorktime $doctorWorktime)
    {
        $date = null;
        $_date = Carbon::tomorrow();
        while (true) {
            if ($_date->dayName === $doctorWorktime->day) {
                $date = $_date;
                break;
            }
            $_date->addDay();
        }

        \DB::transaction(function () use ($doctorWorktime, $date) {
            DoctorWorktime::find($doctorWorktime->replaced_with_id)->update(['active_date' => $date]);
            $doctorWorktime->update(['deleted_at' => $date]);
        });

        return redirect(route('admin@conflict'));
    }

    public function destroy(DoctorWorktime $doctorWorktime)
    {
        // biar di dalam closure tetap bisa akses
        $doctorWorktime->load('replacedWith');

        \DB::transaction(function () use ($doctorWorktime) {
            $doctorWorktime->update(['replaced_with_id' => null]);
            $doctorWorktime->replacedWith->delete();
        });

        return redirect(route('admin@conflict'));
    }
}
