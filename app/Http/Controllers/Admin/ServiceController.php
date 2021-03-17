<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function create()
    {
        $services = Service::all(['name'])->pluck('name');

        return view('admin.new-service', compact('services'));
    }
}
