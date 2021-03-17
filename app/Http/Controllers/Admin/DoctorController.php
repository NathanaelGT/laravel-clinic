<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorService;
use App\Models\DoctorWorktime;
use App\Models\Service;

class DoctorController extends Controller
{
    public function list()
    {
        $hasConflict = DoctorWorktime::hasConflict();

        $services = Service::with([
            'doctorService' => function ($query) {
                $query->orderBy('display_order');
            },
            'doctorService.doctorWorktime' => function ($query) {
                $query->whereNotNull('active_date');
                $query->orderBy('time_start');
            },
            'doctorService.doctorWorktime.replacedWith'
        ])
            ->orderBy('display_order')
            ->get();

        $ids = [];
        $data = $services->reduce(function ($carry, Service $service) use (&$ids) {
            $serviceName = $service->name;

            $schedule = $service->doctorService->reduce(
                function ($carry, DoctorService $doctorService) use ($serviceName, &$ids) {
                    $ids["$serviceName.{$doctorService->doctor_name}"] = $doctorService->id;

                    $carry[$doctorService->doctor_name] = $doctorService->doctorWorktime->reduce(
                        function ($carry, DoctorWorktime $doctorWorktime) {
                            $timeAndQuota = [
                                'id' => $doctorWorktime->id,
                                'time' => $doctorWorktime->time_start . ' - ' . $doctorWorktime->time_end,
                                'quota' => $doctorWorktime->quota,
                                'activeDate' => $doctorWorktime->active_date,
                                'deletedAt' => $doctorWorktime->deleted_at,
                                'replacedWith' => null
                            ];

                            if (!is_null($doctorWorktime->replacedWith)) {
                                $doctorWorktime = $doctorWorktime->replacedWith;
                                $timeAndQuota['replacedWith'] = [
                                    'id' => $doctorWorktime->id,
                                    'time' => $doctorWorktime->time_start . ' - ' . $doctorWorktime->time_end,
                                    'quota' => $doctorWorktime->quota,
                                    'activeDate' => $doctorWorktime->active_date,
                                    'deletedAt' => $doctorWorktime->deleted_at,
                                ];
                            }

                            $array = &$carry[$doctorWorktime->day];
                            if (isset($array)) array_push($array, $timeAndQuota);
                            else $array = [$timeAndQuota];
                            return $carry;
                        }
                    );
                    return $carry;
                }
            );

            // ada kemungkinan null kalo semua jadwal dokternya dihapus
            if ($schedule) {
                $ids[$serviceName] = $service->id;
                $carry[$serviceName] = $schedule;
            }

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
