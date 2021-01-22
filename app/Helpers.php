<?php

namespace App;

class Helpers
{
    public static function timeToNumber(string $time)
    {
        $time = explode(':', $time);
        $hours = (int) $time[0];
        $minutes = (int) $time[1];

        return $hours * 60 + $minutes;
    }

    // rumus jumlah slot
    // $time = explode(' - ', $schedule);
    // $minutes = \App\Helpers::timeToNumber($time[1]) - \App\Helpers::timeToNumber($time[0]);
    // $minutes / $currentSlot;
}