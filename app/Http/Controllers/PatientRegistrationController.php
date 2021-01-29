<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\DoctorService;
use App\Models\Patient;
use App\Models\PatientAppointment;
use App\Models\Service;
use App\Models\ServiceAppointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Redirect;

class PatientRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $tomorrow = Carbon::createMidnightDate()->addDay();
        $service = Service::with([
            'doctorService.doctorWorktime.serviceAppointment' => function($query) use ($tomorrow) {
                $query->where('date', '>=', $tomorrow);
            }
        ])->whereName($request->input('layanan'))->firstOrFail();
        $doctors = $service->doctorService->all();

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
                $sortedDay[$scheduleDayIndex] = $schedule;
            }
            ksort($sortedDay);

            foreach ($sortedDay as $schedule) {
                $quota = $schedule['quota'];
                $start = Helpers::timeToNumber($schedule['time_start']);
                $end = Helpers::timeToNumber($schedule['time_end']);

                $i = 0;
                $times = [];
                for ($time = $start; $time < $end; $time += $quota) {
                    if (
                        isset($schedule['serviceAppointment'][0]) &&
                        (int) $schedule['serviceAppointment'][0]['quota'][$i++] > 0
                    ) continue;
                    array_push($times, Helpers::numberToTimeFormat($time, $time + $quota));
                }

                $day = &$schedules[$index][$schedule['day']];

                if (sizeof($times)) {
                    if (!isset($day)) $day = $times;
                    else array_merge($day, $times);
                }
                else unset($schedules[$index][$schedule['day']]);
            }
        }

        $service = $service['name'];
        $doctors = array_column($doctors, 'doctor_name');
        $formAction = route('patient-registration:store');
        $formMethod = 'POST';

        return view('registration', compact('service', 'doctors', 'schedules', 'formAction', 'formMethod'));
    }

    public function store(Request $request)
    {
        $date = Carbon::parse((int) $request->date);
        $dayName = $date->dayName;

        $service = DoctorService::with(['doctorWorktime' => function($query) use ($dayName) {
            $query->where('day', $dayName);
        }])->whereDoctorName($request->doctor)->first();

        $slots = [];
        $doctorWorktimeId = 0;
        foreach ($service['doctorWorktime'] as $schedule) {
            $patientAppointment = explode(' - ', $request->time);
            if (sizeof($patientAppointment) !== 2)
                return Redirect::back()->with(['error', 'Waktu tidak valid']);

            $quota = $schedule['quota'];
            $patientStart = Helpers::timeToNumber($patientAppointment[0]);
            $patientEnd = Helpers::timeToNumber($patientAppointment[1]);
            if ($patientEnd - $patientStart !== (int) $quota) continue;

            $scheduleStart = Helpers::timeToNumber($schedule['time_start']);
            $scheduleEnd = Helpers::timeToNumber($schedule['time_end']);

            $doctorWorktimeId = $schedule['id'];

            $serviceAppointment = ServiceAppointment::firstOrNew([
                'doctor_worktime_id' => $doctorWorktimeId,
                'date' => $date
            ]);
            if ($serviceAppointment->exists) $slots = $serviceAppointment['quota'];

            $index = 0;
            $patientId = 0;
            for ($time = $scheduleStart; $time < $scheduleEnd; $time += $quota) {
                if ($time === $patientStart) {
                    $patientId = Patient::updateOrCreate([
                        'name' => $request->name,
                        'nik' => $request->nik,
                    ], [
                        'phone_number' => $request->input('phone-number'),
                        'address' => $request->address,
                    ])->id;

                    if (!isset($slots[$index]) || $slots[$index] === '0')
                        $slots[$index] = $patientId;
                    else return Redirect::back()->withErrors(['Slot ini telah dipesan orang lain']);
                }
                elseif (!$serviceAppointment->exists) $slots[$index] = 0;
                $index++;
            }
            $serviceAppointment->quota = $slots;
            $serviceAppointment->save();

            PatientAppointment::create([
                'patient_id' => $patientId,
                'service_appointment_id' => $serviceAppointment['id'],
                'status' => 'Menunggu'
            ]);

            break;
        }

        return redirect(route('home'))->with([
            'message' => "
                Janji temu berhasil dibuat<br/><br/>
                Info:<br/>
                Nama: $request->name<br/>
                NIK: $request->nik<br/>
                No. HP: {$request->input('phone-number')}<br/>
                Alamat: $request->address
            "
        ]);
    }
}
