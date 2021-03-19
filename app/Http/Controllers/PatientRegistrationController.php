<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\AppointmentHistory;
use App\Models\DoctorService;
use App\Models\Patient;
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

    public function store(StoreAppointmentRequest $request)
    {
        $date = Carbon::parse((int) $request->date)->timezone(config('app.timezone'));
        $dayName = $date->dayName;

        $serviceName = explode('?layanan=', URL::previous())[1];
        $service = DoctorService::with(['doctorWorktime' => function ($query) use ($dayName) {
            $query->where('day', $dayName);
        }])->whereDoctorName($request->doctor)->first();

        $seleted = [
            'doctor' => $request->doctor,
            'date' => $request->date,
            'time' => $request->time
        ];

        if (!$service) {
            $doctor = DoctorService::whereDoctorName($request->doctor)->withTrashed()->first(['service_id']);
            if ($doctor) {
                $doctorServices = DoctorService::whereServiceId($doctor->service_id)
                    ->pluck('doctor_name')
                    ->toArray();
            }

            $message = "Layanan Dr. $request->doctor ";
            $message .= $doctor ? 'baru saja dihapus' : 'tidak dapat ditemukan';
            $message .= ', harap pilih jadwal lain';

            if (isset($doctorServices)) {
                if (sizeof($doctorServices) > 2) {
                    $lastDoctor = array_pop($doctorServices);
                    array_push($doctorServices, "dan $lastDoctor");
                    $message .= '. Alternatif yang tersedia: ' . implode(', ', $doctorServices);
                }
                else if (sizeof($doctorServices)) {
                    $message .= '. Alternatif yang tersedia: ' . implode(', ', $doctorServices);
                }
            }

            return $this->redirectInvalid($serviceName, $seleted, 'doctor', $message);
        }

        $patientAppointment = explode(' - ', $request->time);
        if (sizeof($patientAppointment) !== 2) {
            return $this->redirectInvalid($serviceName, $seleted, 'time', 'Waktu tidak valid');
        }
        $patientStart = Helpers::timeToNumber($patientAppointment[0]);
        $patientEnd = Helpers::timeToNumber($patientAppointment[1]);

        $found = false;
        $invalidHour = false;
        $success = false;
        foreach ($service->doctorWorktime as $schedule) {
            if ($patientEnd - $patientStart !== (int) $schedule->quota) continue;
            $found = true;

            $scheduleStart = Helpers::timeToNumber($schedule->time_start);
            $scheduleEnd = Helpers::timeToNumber($schedule->time_end);
            if ($patientStart < $scheduleStart || $patientEnd > $scheduleEnd) {
                $invalidHour = true;
                continue;
            }

            [$timeStart, $timeEnd] = explode(' - ', $request->time);
            $appointment = AppointmentHistory::whereDate('date', $date)
                ->whereDoctorWorktimeId($schedule->id)
                ->whereTimeStart($timeStart)
                ->whereTimeEnd($timeEnd)
                ->exists();

            if ($appointment) {
                return $this->redirectInvalid(
                    $serviceName,
                    $seleted,
                    'time',
                    'Sesi ini baru saja dipesan orang lain, harap pilih jam praktek lain'
                );
            }

            $patientId = Patient::updateOrCreate([
                'name' => $request->name,
                'nik' => $request->nik,
            ], [
                'phone_number' => $request->input('phone-number'),
                'address' => $request->address,
            ])->id;

            AppointmentHistory::create([
                'date' => $date,
                'doctor' => $request->doctor,
                'service' => $serviceName,
                'doctor_service_id' => $service->id,
                'time_start' => $timeStart,
                'time_end' => $timeEnd,
                'doctor_worktime_id' => $schedule->id,
                'patient_name' => $request->name,
                'patient_nik' => $request->nik,
                'patient_phone_number' => $request->input('phone-number'),
                'patient_address' => $request->address,
                'patient_id' => $patientId,
                'status' => 'Menunggu'
            ]);

            $success = true;
            break;
        }

        if (!$success && $invalidHour) {
            return $this->redirectInvalid(
                $serviceName,
                $seleted,
                'time',
                "Layanan dr. $request->doctor baru saja diubah, harap pilih jam praktek lain"
            );
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
            'message' => 
                'Janji temu berhasil dibuat<br/><br/>' .
                'Info:<br/>' .
                'Nama: ' . htmlspecialchars($request->name) . '<br/>' .
                'NIK: ' . htmlspecialchars($request->nik) . '<br/>' .
                'No. HP: ' . htmlspecialchars($request->input('phone-number')) . '<br/>' .
                'Alamat: ' . htmlspecialchars($request->address)
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
