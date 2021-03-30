<?php

namespace App;

use App\Models\AppointmentHistory;
use Illuminate\Support\Collection;

class Conflict {
    private static ?Collection $conflicts = null;

    public static function getDoctorWorktimeId()
    {
        if (is_null(self::$conflicts)) {
            self::$conflicts = AppointmentHistory::whereStatus('Konflik')
                ->get(['doctor_worktime_id'])
                ->pluck('doctor_worktime_id');
        }

        return self::$conflicts;
    }

    public static function contain(int $doctorWorktimeId)
    {
        return self::getDoctorWorktimeId()->contains($doctorWorktimeId);
    }

    public static function any()
    {
        if (is_null(self::$conflicts)) {
            self::getDoctorWorktimeId();
        }

        return self::$conflicts->isNotEmpty();
    }
}
