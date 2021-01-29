<?php

namespace App;

use App\Models\DoctorWorktime;

class Helpers
{
    public static function timeToNumber(string $time)
    {
        $time = explode(':', $time);
        $hours = (int) $time[0];
        $minutes = (int) $time[1];

        return $hours * 60 + $minutes;
    }

    public static function numberToTime(int $time)
    {
        $minutes = $time % 60;
        $hours = ($time - $minutes) / 60;

        if ($minutes < 10) $minutes = '0' . $minutes;
        if ($hours < 10) $hours = '0' . $hours;

        return "$hours:$minutes";
    }

    public static function numberToTimeFormat(int $start, int $end)
    {
        return Helpers::numberToTime($start) . ' - ' . Helpers::numberToTime($end);
    }

    public static function workTime(DoctorWorktime $doctorWorktime)
    {
        $timeStart = Helpers::timeToNumber($doctorWorktime['time_start']);
        $timeEnd = Helpers::timeToNumber($doctorWorktime['time_end']);

        return $timeEnd - $timeStart;
    }
}