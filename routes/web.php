<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceStatusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Device Status Test Routes
|--------------------------------------------------------------------------
|
| Routes for testing device status endpoints
|
*/

// Test page with form
Route::get('/test/device-status', function () {
    return view('test-device-status');
});

// Test routes that call the API endpoints directly
Route::get('/test/device-status/{device_id}', [DeviceStatusController::class, 'getByDeviceId']);
Route::get('/test/device-status/{device_id}/latest', [DeviceStatusController::class, 'getLatestByDeviceId']);
