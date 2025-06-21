<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Temperature;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function storeTemperature(Request $request)
    {
        $validated = $request->validate([
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'air' => 'required|numeric', // <- diperbaiki dari 'require'
            'status' => 'required|in:Normal,Detected',
        ]);

        $record = Temperature::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $record,
        ]);
    }
}
