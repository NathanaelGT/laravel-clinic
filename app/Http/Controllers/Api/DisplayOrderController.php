<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateDisplayOrderRequest;
use App\Models\DoctorService;
use App\Models\Service;

class DisplayOrderController extends Controller
{
    public function reorderDoctorService(UpdateDisplayOrderRequest $request, Service $service)
    {
        $doctorServicesId = DoctorService::whereServiceId($service->id)
            ->orderBy('display_order')
            ->pluck('id');
        $doctorServicesIdArray = $doctorServicesId->toArray();
        $order = $request->order;
        sort($doctorServicesIdArray);
        sort($order);

        if ($doctorServicesIdArray !== $order) {
            return response()->json(['status' => 'error', 'message' => 'Data yang diberikan tidak sesuai']);
        }
        elseif ($doctorServicesId === $request->order) {
            return response()->json(['status' => 'warning', 'message' => 'Tidak ada data yang berubah']);
        }

        \DB::transaction(function () use ($request) {
            foreach ($request->order as $index => $id) {
                $doctorService = DoctorService::find($id);
                if ($doctorService->display_order !== $index) $doctorService->update(['display_order' => $index]);
            }
        });

        return response()->json(['status' => 'success']);
    }

    public function reorderService(UpdateDisplayOrderRequest $request)
    {
        $servicesId = Service::has('doctorService')->orderBy('display_order')->pluck('id');
        $doctorServicesIdArray = $servicesId->toArray();
        $order = $request->order;
        sort($doctorServicesIdArray);
        sort($order);

        if ($doctorServicesIdArray !== $order) {
            return response()->json(['status' => 'error', 'message' => 'Data yang diberikan tidak sesuai']);
        }
        elseif ($servicesId === $request->order) {
            return response()->json(['status' => 'warning', 'message' => 'Tidak ada data yang berubah']);
        }

        \DB::transaction(function () use ($request) {
            foreach ($request->order as $index => $id) {
                $service = Service::find($id);
                if ($service->display_order !== $index) $service->update(['display_order' => $index]);
            }
        });

        return response()->json(['status' => 'success']);
    }
}
