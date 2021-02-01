<?php

namespace App\Http\Controllers\Admin;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Models\DoctorService;
use App\Models\Patient;
use App\Models\PatientAppointment;
use App\Models\ServiceAppointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Redirect;

class PatientController extends Controller
{
    public function list()
    {
        $service = 'service_appointments';
        $patients = PatientAppointment::with(
            'patient',
            'serviceAppointment.doctorWorktime.doctorService.service'
        )->whereStatus('Menunggu')
        ->join($service, "$service.id", '=', 'patient_appointments.service_appointment_id')
        ->orderBy("$service.date")
        ->select('patient_appointments.*')
        ->get()->toArray();

        return view('admin.patient-list', compact('patients'));
    }

    public function reschedule(Request $request, PatientAppointment $patientAppointment)
    {
        $doctorWorktime = $patientAppointment['serviceAppointment']['doctorWorktime'];
        $service = $doctorWorktime['doctorService']['service']['name'];
        $schedules = Helpers::getSchedule($service);
        $doctors = array_column(Helpers::$serviceSchedule->doctorService->all(), 'doctor_name');
        $formAction = route('admin@patient-reschedule:put');
        $formMethod = 'PUT';

        $patient = [
            'name' => $patientAppointment['patient']['name'],
            'nik' => $patientAppointment['patient']['nik'],
            'phone_number' => $patientAppointment['patient']['phone_number'],
            'address' => $patientAppointment['patient']['address'],
            'doctor' => $doctorWorktime['doctorService']['doctor_name']
        ];

        $seleted = [
            'doctor' => $doctorWorktime['doctorService']['doctor_name'],
            'date' => Carbon::parse($patientAppointment['serviceAppointment']['date'])->isoFormat('X'),
            'time' => implode(' - ', Helpers::getPatientMeetHour($doctorWorktime, $patientAppointment))
        ];

        return view(
            'registration',
            compact('service', 'doctors', 'schedules', 'formAction', 'formMethod', 'patient', 'seleted')
        );
    }

    public function update(Request $request)
    {
        $PatientAppointment = PatientAppointment::with('patient', 'serviceAppointment')
            ->findOrFail(explode('/', URL::previous())[5]);

        $date = Carbon::parse((int) $request->date);
        $dayName = $date->dayName;

        $service = DoctorService::with(['doctorWorktime' => function($query) use ($dayName) {
            $query->where('day', $dayName);
        }])->whereDoctorName($request->doctor)->first();

        \DB::transaction(function () use ($service, $request, $PatientAppointment, $date) {
            $slots = [];
            $doctorWorktimeId = 0;
            $patientId = $PatientAppointment['patient']['id'];
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
                for ($time = $scheduleStart; $time < $scheduleEnd; $time += $quota) {
                    if ($time === $patientStart) {
                        if (!isset($slots[$index]) || $slots[$index] === '0')
                            $slots[$index] = $patientId;
                        else return Redirect::back()->withErrors(['Slot ini telah dipesan orang lain']);
                    }
                    elseif (!$serviceAppointment->exists) $slots[$index] = 0;
                    $index++;
                }
                // FIXME
                $oldServiceAppointment = $PatientAppointment['serviceAppointment'];
                $quota = $oldServiceAppointment['quota'];
                $quotaIndex = array_search($patientId, $quota);
                $quota[$quotaIndex] = '0';
                ServiceAppointment::find($oldServiceAppointment['id'])->update($quota);

                $serviceAppointment->quota = $slots;
                $serviceAppointment->save();

                $PatientAppointment->service_appointment_id = $serviceAppointment['id'];
                $PatientAppointment->save();

                break;
            }
        });

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
