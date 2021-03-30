<?php

namespace App\Http\Controllers\Admin;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\AppointmentHistory;
use App\Models\DoctorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function list()
    {
        $appointments = AppointmentHistory::with('patient', 'doctorService.service')
            ->whereIn('status', ['Menunggu', 'Konflik'])
            ->orderBy('date')
            ->orderBy('time_start')
            ->get();

        return view('admin.patient-list', compact('appointments'));
    }

    public function done($id)
    {
        AppointmentHistory::whereStatus('Menunggu')
            ->whereId($id)
            ->update(['status' => 'Selesai']);

        return redirect(route('admin@patient-list'));
    }

    public function cancel($id)
    {
        AppointmentHistory::whereIn('status', ['Menunggu', 'Konflik'])
            ->whereId($id)
            ->update(['status' => 'Dibatalkan']);

        return redirect(route('admin@patient-list'));
    }

    public function reschedule($id)
    {
        $appointment = AppointmentHistory::whereIn('status', ['Menunggu', 'Konflik'])
            ->whereId($id)
            ->with([
                'doctorWorktime' => function ($query) {
                    $query->withTrashed();
                },
                'doctorWorktime.doctorService.service'
            ])
            ->firstOrFail();

        $doctorWorktime = $appointment->doctorWorktime;
        $service = $doctorWorktime->doctorService->service->name;
        $schedules = Helpers::getSchedule($service, $doctorWorktime->replaced_with_id);
        $doctors = array_column(Helpers::$serviceSchedule->doctorService->all(), 'doctor_name');
        $formAction = route('admin@patient-reschedule:put', $appointment->id);
        $formMethod = 'PUT';

        $patient = [
            'name' => $appointment->patient->name,
            'nik' => $appointment->patient->nik,
            'phone_number' => $appointment->patient->phone_number,
            'address' => $appointment->patient->address,
            'doctor' => $doctorWorktime->doctorService->doctor_name
        ];

        $selected = [
            'doctor' => $doctorWorktime->doctorService->doctor_name,
            'date' => $appointment->date->isoFormat('X'),
            'time' => $appointment->time_start . ' - ' . $appointment->time_end
        ];

        return view(
            'registration',
            compact('service', 'doctors', 'schedules', 'formAction', 'formMethod', 'patient', 'selected')
        );
    }

    public function update(UpdatePatientRequest $request, $id)
    {
        [$timeStart, $timeEnd] = explode(' - ', $request->time);
        if (!$timeEnd) {
            return back()->withErrors(['time' => 'Waktu tidak valid']);
        }

        $appointment = AppointmentHistory::whereIn('status', ['Menunggu', 'Konflik'])
            ->whereId($id)
            ->firstOrFail();

        $date = Carbon::parse((int) $request->date)->timezone(config('app.timezone'));
        $otherAppointment = AppointmentHistory::whereDate('date', $date)
            ->whereDoctorServiceId($appointment->doctor_service_id)
            ->whereTimeStart($timeStart)
            ->whereTimeEnd($timeEnd)
            ->exists();

        if ($otherAppointment) {
            return back()->withErrors(['time' => 'Slot ini telah dipesan orang lain']);
        }

        $date = Carbon::parse((int) $request->date)->timezone(config('app.timezone'));
        $dayName = $date->dayName;
        $service = DoctorService::whereDoctorName($request->doctor)
            ->with(['doctorWorktime' => function ($query) use ($dayName) {
                $query->where('day', $dayName);
            }])
            ->first();

        if (!$service) {
            return back()->withErrors(['doctor' => "Layanan dokter $request->doctor tidak dapat ditemukan"]);
        }

        $patientAppointment = explode(' - ', $request->time);
        if (sizeof($patientAppointment) !== 2) {
            return back()->withErrors(['time' => 'Waktu tidak valid']);
        }
        $patientStart = Helpers::timeToNumber($patientAppointment[0]);
        $patientEnd = Helpers::timeToNumber($patientAppointment[1]);

        $found = false;
        foreach ($service->doctorWorktime as $schedule) {
            if ($patientEnd - $patientStart !== (int) $schedule->quota) continue;
            $found = true;

            DB::transaction(function () use ($appointment, $date, $timeStart, $timeEnd, $schedule) {
                $now = now();

                $rescheduleId = AppointmentHistory::insertGetId(
                    array_merge($appointment->toArray(), [
                        'id' => null,
                        'date' => $date,
                        'time_start' => $timeStart,
                        'time_end' => $timeEnd,
                        'doctor_worktime_id' => $schedule->id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ])
                );
                $appointment->update([
                    'status' => 'Reschedule',
                    'reschedule_id' => $rescheduleId
                ]);
            });

            break;
        }

        if (!$found) {
            return back()->withErrors([
                'date' => "Layanan dr. $request->doctor baru saja dihapus, harap pilih jadwal lain"
            ]);
        }

        return redirect(route('admin@patient-list'));
    }

    public function log()
    {
        $appointments = AppointmentHistory::with('patient', 'doctorService.service')
            ->whereNotIn('status', ['Menunggu', 'Konflik'])
            ->orderBy('date')
            ->paginate(10);

        return view('admin.appointment-log', compact('appointments'));
    }
}
