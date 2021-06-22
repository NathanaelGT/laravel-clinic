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

        if ($minutes < 10) {
            $minutes = '0' . $minutes;
        }
        if ($hours < 10) {
            $hours = '0' . $hours;
        }

        return "$hours:$minutes";
    }

    public static function numberToTimeFormat(int $start, int $end)
    {
        return self::numberToTime($start) . ' - ' . self::numberToTime($end);
    }

    public static function timeFormatToNumber(string $time)
    {
        [$start, $end] = explode('-', $time);

        return self::timeToNumber($end) - self::timeToNumber($start);
    }

    public static function formatSlotTime(int $slot, string $time)
    {
        $time = self::timeFormatToNumber($time);
        if ($slot === $time) return '1 sesi';

        $minutes = $slot % 60;
        $hours = ($slot - $minutes) / 60;
        if (!$minutes) {
            return "$hours jam";
        } elseif (!$hours) {
            return "$minutes menit";
        }
        return "$hours jam $minutes menit";
    }


    public static function workTime(DoctorWorktime $doctorWorktime)
    {
        $timeStart = self::timeToNumber($doctorWorktime['time_start']);
        $timeEnd = self::timeToNumber($doctorWorktime['time_end']);

        return $timeEnd - $timeStart;
    }

    public static function getPatientMeetHour($doctorWorktime, $patientAppointment)
    {
        if (!is_array($patientAppointment)) {
            $patientAppointment = $patientAppointment->toArray();
        }

        $slotIndex = array_search(
            $patientAppointment['patient_id'],
            $patientAppointment['service_appointment']['quota']
        );

        $timeStart = self::timeToNumber($doctorWorktime['time_start']);
        $timeStart += $slotIndex * $doctorWorktime['quota'];

        $timeEnd = self::numberToTime($timeStart + $doctorWorktime['quota']);
        $timeStart = self::numberToTime($timeStart);

        return [$timeStart, $timeEnd];
    }


    public static $serviceSchedule;
    public static function getSchedule(string $serviceName, int $exceptionId = null)
    {
        $tomorrow = Carbon::tomorrow();
        $today = Carbon::createMidnightDate();
        self::$serviceSchedule = Service::with([
            'doctorService.doctorWorktime' => function ($query) use ($today, $exceptionId) {
                $query->whereNull('replaced_with_id');
                $query->OrWhereNull('deleted_at');

                $query->where('active_date', '<=', $today);

                if ($exceptionId) {
                    $query->orWhere('id', $exceptionId);
                }

                $query->orderBy('time_start');
            },
            'doctorService.doctorWorktime.appointmentHistory' => function ($query) {
                $query->whereIn('status', ['Menunggu', 'Konflik']);
                $query->orderBy('time_start');
            }
        ])->whereName($serviceName)->firstOrFail();

        $doctors = self::$serviceSchedule->doctorService->all();

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
                if (isset($sortedDay[$scheduleDayIndex])) {
                    array_push($sortedDay[$scheduleDayIndex], $schedule);
                } else {
                    $sortedDay[$scheduleDayIndex] = [$schedule];
                }
            }
            ksort($sortedDay);

            foreach ($sortedDay as $scheduleDay) {
                foreach ($scheduleDay as $schedule) {
                    $quota = $schedule['quota'];
                    $start = self::timeToNumber($schedule['time_start']);
                    $end = self::timeToNumber($schedule['time_end']);

                    $taken = [];
                    foreach ($schedule->appointmentHistory as $appointment) {
                        $timeStart = $appointment['time_start'];
                        $timeStart = self::timeToNumber($timeStart);
                        $taken[$timeStart] = true;
                    }

                    $times = [];
                    for ($time = $start; $time < $end; $time += $quota) {
                        if (isset($taken[$time])) continue;
                        array_push($times, self::numberToTimeFormat($time, $time + $quota));
                    }

                    $time = $schedule['day'];
                    if ($schedule['id'] === $exceptionId) {
                        $time .= 'exception';
                    }

                    $day = &$schedules[$index][$time];

                    if (sizeof($times)) {
                        $day = isset($day) ? array_merge($day, $times) : $times;
                    } else {
                        unset($schedules[$index][$time]);
                    }
                }
            }
        }

        return $schedules;
    }
}
