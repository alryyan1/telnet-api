<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MorphoAuthController;
use App\Http\Controllers\MorphoDeviceController;
use App\Http\Controllers\DeviceStatusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Morpho IoT Device Integration Routes
|--------------------------------------------------------------------------
|
| Routes for integrating with Morpho IoT Device API
| Accessible at: domain/api/device/status, etc.
| Note: Auth route is registered separately without api prefix at domain/auth
|
*/

// Device endpoints (require JWT authentication)
Route::prefix('device')->middleware('jwt.auth')->group(function () {
    Route::post('/status', [MorphoDeviceController::class, 'sendStatus']);
    Route::post('/logs', [MorphoDeviceController::class, 'sendLogs']);
    Route::get('/config', [MorphoDeviceController::class, 'getConfig']);
    Route::post('/config', [MorphoDeviceController::class, 'updateConfig']); // Platform: Update configuration
    Route::get('/reboot', [MorphoDeviceController::class, 'getRebootCommand']); // Device: Check for reboot
    Route::post('/reboot', [MorphoDeviceController::class, 'requestReboot']); // Platform: Request reboot
});

/*
|--------------------------------------------------------------------------
| Device Status Routes
|--------------------------------------------------------------------------
|
| Routes for retrieving device statuses by device_id
| Accessible at: domain/api/device-statuses/{device_id}
|
*/

Route::prefix('device-statuses')->group(function () {
    Route::get('/{device_id}', [DeviceStatusController::class, 'getByDeviceId']);
    Route::get('/{device_id}/latest', [DeviceStatusController::class, 'getLatestByDeviceId']);
});
