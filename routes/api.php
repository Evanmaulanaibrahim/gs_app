<?php

use App\Http\Controllers\Api\RFIDAccessController;
use App\Http\Controllers\Api\SensorController;
use App\Models\AccessLog;
use App\Models\EmployeeRFID;
use App\Models\Temperature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::middleware('api')->prefix('sensor')->group(function () {
//     Route::post('/temperature', function (Request $request) {
//         // Validasi data
//         $validated = $request->validate([
//             'temperature' => 'required|numeric',
//             'humidity' => 'required|numeric',
//             'air' => 'required|numeric',
//             'status' => 'required|in:Normal,Detected'
//         ]);
        
//         // Simpan data ke database
//         $record = Temperature::create($validated);
        
//         return response()->json([
//             'success' => true,
//             'message' => 'Data berhasil disimpan',
//             'data' => $record
//         ]);
//     });
// });

Route::prefix('sensor')->group(function () {
    Route::post('/temperature', [SensorController::class, 'storeTemperature']);
});

Route::post('/rfid/check-access', [RFIDAccessController::class, 'checkAccess']);