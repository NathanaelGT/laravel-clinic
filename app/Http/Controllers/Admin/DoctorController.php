<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conflict;
use App\Models\DoctorService;
use App\Models\Service;
use App\Models\ServiceAppointment;
use Carbon\Carbon;

class DoctorController extends Controller
{
    public function list()
    {
        $conflicts = Conflict::with('serviceAppointment')->get();

        foreach ($conflicts as $conflict) {
            $conflictDate = Carbon::parse($conflict->serviceAppointment->date);
            if ($conflictDate->isPast()) $conflict->delete();
        }
        $hasConflict = ServiceAppointment::where('date', '>', Carbon::today())->has('conflict')->exists();

        $services = Service::with([
            'doctorService' => fn($query) => $query->orderBy('display_order'),
            'doctorService.doctorWorktime'
        ])
            ->orderBy('display_order')
            ->get()
            ->toArray();

        $ids = [];
        $data = array_reduce($services, function($carry, $service) use (&$ids) {
            $serviceName = $service['name'];
            $array = &$carry[$serviceName];

            $array = [];
            $ids[$serviceName] = $service['id'];

            $array = array_reduce(
                $service['doctor_service'],
                function($carry, $doctorService) use ($serviceName, &$ids) {
                    $ids["$serviceName.{$doctorService['doctor_name']}"] = $doctorService['id'];

                    $carry[$doctorService['doctor_name']] = array_reduce(
                        $doctorService['doctor_worktime'],
                        function ($carry, $schedule) {
                            $array = &$carry[$schedule['day']];
                            $timeAndQuota = [
                                'id' => $schedule['id'],
                                'time' => $schedule['time_start'] . ' - ' . $schedule['time_end'],
                                'quota' => $schedule['quota'],
                                'activeDate' => Carbon::parse($schedule['active_date']),
                                'deletedAt' => $schedule['deleted_at']
                            ];

                            if (isset($array)) array_push($array, $timeAndQuota);
                            else $array = [$timeAndQuota];
                            return $carry;
                        }
                    );
                    return $carry;
                }
            );
            return $carry;
        });

        return view('admin.doctor-list', compact('data', 'ids', 'hasConflict'));
    }

    public function delete(DoctorService $doctorService)
    {
        $doctorService->delete();
        return redirect()->route('admin@doctor-list');
    }
}
