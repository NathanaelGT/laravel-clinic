<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\DoctorService;
use App\Models\Patient;
use App\Models\PatientAppointment;
use App\Models\ServiceAppointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class PatientRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Helpers::getSchedule($request->input('layanan'));
        $service = $request->input('layanan');
        $doctors = array_column(Helpers::$serviceSchedule->doctorService->all(), 'doctor_name');
        $formAction = route('patient-registration:store');
        $formMethod = 'POST';

        return view('registration', compact('service', 'doctors', 'schedules', 'formAction', 'formMethod'));
    }

    public function store(Request $request)
    {
        $date = Carbon::parse((int) $request->date)->addHours(8);
        $dayName = $date->dayName;

        $serviceName = explode('?layanan=', URL::previous())[1];
        $service = DoctorService::with(['doctorWorktime' => function($query) use ($dayName) {
            $query->where('day', $dayName);
        }])->whereDoctorName($request->doctor)->first();

        $seleted = [
            'doctor' => $request->doctor,
            'date' => $request->date,
            'time' => $request->time
        ];

        if (!$service) {
            $doctorServices = DoctorService::with(['service' => function($query) use ($serviceName) {
                $query->where('name', $serviceName);
            }])->pluck('doctor_name')->toArray();

            $message = "Layanan Dr. $request->doctor baru saja dihapus, harap pilih jadwal lain";
            if (sizeof($doctorServices) > 2) {
                $lastDoctor = array_pop($doctorServices);
                array_push($doctorServices, "dan $lastDoctor");
                $message .= '. Alternatif lainnya: ' . implode(', ', $doctorServices);
            }
            else if (sizeof($doctorServices)) {
                $message .= '. Alternatif lainnya: ' . implode(', ', $doctorServices);
            }

            return $this->redirectInvalid($serviceName, $seleted, 'doctor', $message);
        }

        $slots = [];
        $doctorWorktimeId = 0;
        $found = false;
        foreach ($service->doctorWorktime as $schedule) {
            $patientAppointment = explode(' - ', $request->time);
            if (sizeof($patientAppointment) !== 2) {
                return $this->redirectInvalid($serviceName, $seleted, 'time', 'Waktu tidak valid');
            }

            $quota = $schedule->quota;
            $patientStart = Helpers::timeToNumber($patientAppointment[0]);
            $patientEnd = Helpers::timeToNumber($patientAppointment[1]);
            if ($patientEnd - $patientStart !== (int) $quota) continue;
            $found = true;

            $scheduleStart = Helpers::timeToNumber($schedule->time_start);
            $scheduleEnd = Helpers::timeToNumber($schedule->time_end);
            if ($patientStart < $scheduleStart || $patientEnd > $scheduleEnd) {
                return $this->redirectInvalid(
                    $serviceName,
                    $seleted,
                    'time',
                    "Layanan dr. $request->doctor baru saja diubah, harap pilih jam praktek lain"
                );
            }

            $doctorWorktimeId = $schedule->id;

            $serviceAppointment = ServiceAppointment::firstOrNew([
                'doctor_worktime_id' => $doctorWorktimeId,
                'date' => $date
            ]);
            if ($serviceAppointment->exists) $slots = $serviceAppointment->quota;

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

                    if (!isset($slots[$index]) || $slots[$index] === '0') $slots[$index] = $patientId;
                    else {
                        return $this->redirectInvalid(
                            $serviceName,
                            $seleted,
                            'time',
                            'Sesi ini baru saja dipesan orang lain, harap pilih jam praktek lain'
                        );
                    }
                }
                elseif (!$serviceAppointment->exists) $slots[$index] = 0;
                $index++;
            }
            $serviceAppointment->quota = $slots;
            $serviceAppointment->save();

            PatientAppointment::create([
                'patient_id' => $patientId,
                'service_appointment_id' => $serviceAppointment->id,
                'status' => 'Menunggu'
            ]);

            break;
        }

        if (!$found) {
            return $this->redirectInvalid(
                $serviceName,
                $seleted,
                'date',
                "Layanan dr. $request->doctor baru saja dihapus, harap pilih jadwal lain"
            );
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

    private function redirectInvalid(string $service, array $selected, string $input, string $message)
    {
        $schedules = Helpers::getSchedule(urldecode($service));
        $doctors = array_column(Helpers::$serviceSchedule->doctorService->all(), 'doctor_name');
        $formAction = route('patient-registration:store');
        $formMethod = 'POST';

        Session::flash($input, $message);
        return view(
            'registration',
            compact('service', 'doctors', 'schedules', 'formAction', 'formMethod', 'selected')
        );
    }
}
