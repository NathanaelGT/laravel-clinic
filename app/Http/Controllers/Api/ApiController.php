<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function doctor(Request $request, $id)
    {
        // TODO update nama dokter
        return response()->json(['id' => $id, 'data' => json_decode($request->getContent())]);
    }

    public function serviceTime(Request $request, $id)
    {
        // TODO update nama dokter
        if ($id === 'new') {
            return response()->json(['id' => 100, 'data' => json_decode($request->getContent())]);
        }

        return response()->json(['id' => $id, 'data' => json_decode($request->getContent())]);
    }

    public function servicePer(Request $request, $id)
    {
        // TODO update nama dokter
        if ($id === 'new') {
            return response()->json(['id' => 100, 'data' => json_decode($request->getContent())]);
        }

        return response()->json(['id' => $id, 'data' => json_decode($request->getContent())]);
    }

    public function close(Request $request, $id)
    {
        // TODO update nama dokter
        return response()->json(['id' => $id, 'data' => json_decode($request->getContent())]);
    }

    public function delete(Request $request, $id)
    {
        // TODO update nama dokter
        return response()->json(['id' => $id, 'data' => json_decode($request->getContent())]);
    }
}
