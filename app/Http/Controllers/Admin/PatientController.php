<?php

namespace App\Http\Controllers\Admin;

use App\Helpers;
use App\Http\Controllers\Controller;
use App\Models\AppointmentHistory;
use App\Models\DoctorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function list()
    {
        $appointments = AppointmentHistory::with('patient', 'doctorService.service')
            ->whereStatus('Menunggu')
            ->orderBy('date')
            ->orderBy('time_start')
            ->get();

        return view('admin.patient-list', compact('appointments'));
    }

    public function done(AppointmentHistory $appointmentHistory)
    {
        $appointmentHistory->update(['status' => 'Selesai']);

        return redirect(route('admin@patient-list'));
    }

    public function cancel(AppointmentHistory $appointmentHistory)
    {
        $appointmentHistory->update(['status' => 'Dibatalkan']);

        return redirect(route('admin@patient-list'));
    }

    public function reschedule(AppointmentHistory $appointmentHistory)
    {
        $appointmentHistory->load([
            'doctorWorktime' => function ($query) {
                $query->withTrashed();
            },
            'doctorWorktime.doctorService.service'
        ]);

        $doctorWorktime = $appointmentHistory->doctorWorktime;
        $service = $doctorWorktime->doctorService->service->name;
        $schedules = Helpers::getSchedule($service, $doctorWorktime->replaced_with_id);
        $doctors = array_column(Helpers::$serviceSchedule->doctorService->all(), 'doctor_name');
        $formAction = route('admin@patient-reschedule:put', $appointmentHistory->id);
        $formMethod = 'PUT';

        $patient = [
            'name' => $appointmentHistory->patient->name,
            'nik' => $appointmentHistory->patient->nik,
            'phone_number' => $appointmentHistory->patient->phone_number,
            'address' => $appointmentHistory->patient->address,
            'doctor' => $doctorWorktime->doctorService->doctor_name
        ];

        $selected = [
            'doctor' => $doctorWorktime->doctorService->doctor_name,
            'date' => $appointmentHistory->date->isoFormat('X'),
            'time' => $appointmentHistory->time_start . ' - ' . $appointmentHistory->time_end
        ];

        return view(
            'registration',
            compact('service', 'doctors', 'schedules', 'formAction', 'formMethod', 'patient', 'selected')
        );
    }

    public function update(Request $request, AppointmentHistory $appointmentHistory)
    {
        // dd($request->all());
        [$timeStart, $timeEnd] = explode(' - ', $request->time);
        if (!$timeEnd) {
            return back()->withErrors(['time' => 'Waktu tidak valid']);
        }

        $date = Carbon::parse((int) $request->date)->timezone(config('app.timezone'));
        $appointment = AppointmentHistory::whereDate('date', $date)
            ->whereDoctorServiceId($appointmentHistory->doctor_service_id)
            ->whereTimeStart($timeStart)
            ->whereTimeEnd($timeEnd)
            ->exists();

        if ($appointment) {
            return back()->withErrors(['time' => 'Slot ini telah dipesan orang lain']);
        }

        $date = Carbon::parse((int) $request->date)->timezone(config('app.timezone'));
        $dayName = $date->dayName;
        $service = DoctorService::with(['doctorWorktime' => function ($query) use ($dayName) {
            $query->where('day', $dayName);
        }])->whereDoctorName($request->doctor)->first();

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

            DB::transaction(function () use ($appointmentHistory, $date, $timeStart, $timeEnd, $schedule) {
                $now = now();

                $rescheduleId = AppointmentHistory::insertGetId(
                    array_merge($appointmentHistory->toArray(), [
                        'id' => null,
                        'date' => $date,
                        'time_start' => $timeStart,
                        'time_end' => $timeEnd,
                        'doctor_worktime_id' => $schedule->id,
                        'created_at' => $now,
                        'updated_at' => $now
                    ])
                );
                $appointmentHistory->update([
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
            ->where('status', '!=', 'Menunggu')
            ->orderBy('date')
            ->paginate(10);

        return view('admin.appointment-log', compact('appointments'));
    }
}
