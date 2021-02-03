<?php

namespace App;

use App\Models\DoctorWorktime;
use App\Models\Service;
use Carbon\Carbon;

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

    public static function getPatientMeetHour($doctorWorktime, $patientAppointment)
    {
        if (!is_array($patientAppointment)) $patientAppointment = $patientAppointment->toArray();

        $slotIndex = array_search(
            $patientAppointment['patient_id'],
            $patientAppointment['service_appointment']['quota']
        );

        $timeStart = Helpers::timeToNumber($doctorWorktime['time_start']);
        $timeStart += $slotIndex * $doctorWorktime['quota'];

        $timeEnd = Helpers::numberToTime($timeStart + $doctorWorktime['quota']);
        $timeStart = Helpers::numberToTime($timeStart);

        return [$timeStart, $timeEnd];
    }

    public static $serviceSchedule = null;
    public static function getSchedule(string $serviceName)
    {
        $tomorrow = Carbon::tomorrow();
        Helpers::$serviceSchedule = Service::with([
            'doctorService.doctorWorktime.serviceAppointment' => function($query) use ($tomorrow) {
                $query->where('date', '>=', $tomorrow);
            }
        ])->whereName($serviceName)->firstOrFail();
        $doctors = Helpers::$serviceSchedule->doctorService->all();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $tomorrowIndex = array_search($tomorrow->dayName, $days);
        $days = array_merge(array_splice($days, $tomorrowIndex), $days);

        $schedules = [];
        foreach ($doctors as $index => $doctor) {
            if (!isset($schedules[$index])) $schedules[$index] = [];

            $sortedDay = [];
            foreach ($doctor->doctorWorktime as $schedule) {
                $scheduleDayIndex = array_search($schedule['day'], $days);
                $scheduleDate = $tomorrow->copy()->addDays($scheduleDayIndex);

                $schedule['day'] = $scheduleDate->isoFormat('X');
                if (isset($sortedDay[$scheduleDayIndex])) array_push($sortedDay[$scheduleDayIndex], $schedule);
                else $sortedDay[$scheduleDayIndex] = [$schedule];
            }
            ksort($sortedDay);

            foreach ($sortedDay as $scheduleDay) {
                foreach ($scheduleDay as $schedule) {
                    $quota = $schedule['quota'];
                    $start = Helpers::timeToNumber($schedule['time_start']);
                    $end = Helpers::timeToNumber($schedule['time_end']);

                    $i = 0;
                    $times = [];
                    for ($time = $start; $time < $end; $time += $quota) {
                        if (
                            isset($schedule['serviceAppointment'][0]) &&
                            $schedule['serviceAppointment'][0]['quota'][$i++]
                        ) continue;
                        array_push($times, Helpers::numberToTimeFormat($time, $time + $quota));
                    }

                    $day = &$schedules[$index][$schedule['day']];

                    if (sizeof($times)) {
                        if (!isset($day)) $day = $times;
                        else $day = array_merge($day, $times);
                    }
                    else unset($schedules[$index][$schedule['day']]);
                }
            }
        }

        return $schedules;
    }
}