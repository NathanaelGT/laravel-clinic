<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conflict;
use App\Models\DoctorWorktime;
use App\Models\ServiceAppointment;
use Carbon\Carbon;

class ConflictController extends Controller
{
    public function list()
    {
        $service = 'service_appointments';
        $conflicts = Conflict::with('serviceAppointment.patientAppointment.patient', 'doctorWorktime.doctorService')
            ->join($service, "$service.id", '=', 'conflicts.service_appointment_id')
            ->orderBy("$service.date")
            ->select('conflicts.*')
            ->get();

        return view('admin.conflict-list', compact('conflicts'));
    }

    public function closeRegistration(ServiceAppointment $serviceAppointment)
    {
        $newQuota = array_map(fn ($value) => $value ?: '-1', $serviceAppointment['quota']);
        $serviceAppointment->quota = $newQuota;
        $serviceAppointment->save();

        return redirect(route('admin@conflict'));
    }

    public function nextWeek(Conflict $conflict)
    {
        \DB::transaction(function() use ($conflict) {
            $serviceAppointment = $conflict['serviceAppointment'];
            $nextWeek = Carbon::parse($serviceAppointment['date'])->addWeek();

            $serviceAppointment->quota = array_map(
                fn ($value) => $value === '-1' ? 0 : $value,
                $serviceAppointment['quota']
            );
            $serviceAppointment->save();

            $conflict->doctorWorktime->deleted_at = $nextWeek;
            $conflict->doctorWorktime->save();

            DoctorWorktime::create([
                'doctor_service_id' => $conflict['doctorWorktime']['doctor_service_id'],
                'quota' => $conflict['quota'],
                'time_start' => $conflict['time_start'],
                'time_end' => $conflict['time_end'],
                'active_date' => $nextWeek
            ]);

            $conflict->delete();
        });

        return redirect(route('admin@conflict'));
    }

    public function destroy(Conflict $conflict)
    {
        $conflict->delete();

        return redirect(route('admin@conflict'));
    }
}
