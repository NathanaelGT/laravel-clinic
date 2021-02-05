<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conflict;
use App\Models\DoctorService;
use App\Models\ServiceAppointment;
use Carbon\Carbon;

class DoctorController extends Controller
{
    public function list()
    {
        $conflicts = Conflict::with('serviceAppointment')->get();

        foreach ($conflicts as $conflict) {
            $conflictDate = Carbon::parse($conflict->serviceAppointment->date);
            if ($conflictDate->addDay()->isPast()) $conflict->delete();
        }
        $hasConflict = ServiceAppointment::where('date', '>', Carbon::today())->has('conflict')->exists();

        $services = DoctorService::with('doctorWorktime', 'service')->get()->toArray();

        $ids = [];
        $data = array_reduce($services, function($carry, $item) use (&$ids) {
            $serviceName = $item['service']['name'];
            $array = &$carry[$serviceName];

            if (!isset($array)) $array = [];
            $ids["$serviceName.{$item['doctor_name']}"] = $item['id'];

            $array[$item['doctor_name']] = array_reduce($item['doctor_worktime'], function($carry, $item) {
                $array = &$carry[$item['day']];
                $timeAndQuota = [
                    'id' => $item['id'],
                    'time' => $item['time_start'] . ' - ' . $item['time_end'],
                    'quota' => $item['quota'],
                    'activeDate' => Carbon::parse($item['active_date']),
                    'deletedAt' => $item['deleted_at']
                ];

                if (!isset($array)) $array = [$timeAndQuota];
                else array_push($array, $timeAndQuota);

                return $carry;
            }, []);

            return $carry;
        }, []);

        return view('admin.doctor-list', compact('data', 'ids', 'hasConflict'));
    }

    public function delete(DoctorService $doctorService)
    {
        $doctorService->delete();
        return redirect()->route('admin@doctor-list');
    }
}
