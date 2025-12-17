<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendDeviceStatusRequest;
use App\Http\Requests\SendDeviceLogsRequest;
use App\Http\Requests\UpdateDeviceConfigRequest;
use App\Services\MorphoApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MorphoDeviceController extends Controller
{
    protected MorphoApiService $morphoApiService;

    public function __construct(MorphoApiService $morphoApiService)
    {
        $this->morphoApiService = $morphoApiService;
    }

    /**
     * Send device status
     * Device periodically pushes telemetry (GPS, sensors, firmware info, battery, etc.)
     *
     * @param SendDeviceStatusRequest $request
     * @return JsonResponse
     */
    public function sendStatus(SendDeviceStatusRequest $request): JsonResponse
    {
        $result = $this->morphoApiService->sendDeviceStatus($request->validated());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Device status received and stored successfully',
                'data' => $result['data'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to store device status',
            'error' => $result['error'] ?? 'Unknown error',
        ], 500);
    }

    /**
     * Send device logs
     * Device sends log entries with severity, message, and context
     *
     * @param SendDeviceLogsRequest $request
     * @return JsonResponse
     */
    public function sendLogs(SendDeviceLogsRequest $request): JsonResponse
    {
        $result = $this->morphoApiService->sendDeviceLogs($request->validated());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Device log received and stored successfully',
                'data' => $result['data'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to store device log',
            'error' => $result['error'] ?? 'Unknown error',
        ], 500);
    }

    /**
     * Fetch device configuration
     * Device pulls new configuration using GET
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getConfig(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|integer',
        ]);

        $result = $this->morphoApiService->fetchDeviceConfig($request->input('device_id'));

        if ($result['success']) {
            // Return configuration data directly (as per API spec)
            return response()->json($result['data'], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch device configuration',
            'error' => $result['error'] ?? 'Unknown error',
        ], 404);
    }

    /**
     * Update device configuration
     * Platform updates configuration via POST (admin UI or API)
     *
     * @param UpdateDeviceConfigRequest $request
     * @return JsonResponse
     */
    public function updateConfig(UpdateDeviceConfigRequest $request): JsonResponse
    {
        $deviceId = $request->input('device_id');
        $result = $this->morphoApiService->updateDeviceConfig($deviceId, $request->validated());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Device configuration updated successfully',
                'data' => $result['data'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update device configuration',
            'error' => $result['error'] ?? 'Unknown error',
        ], 500);
    }

    /**
     * Fetch reboot command (Device checks for pending reboot requests)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getRebootCommand(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|integer',
        ]);

        $result = $this->morphoApiService->fetchRebootCommand($request->input('device_id'));

        if ($result['success']) {
            // Return reboot command data directly
            return response()->json($result['data'], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch reboot command',
            'error' => $result['error'] ?? 'Unknown error',
        ], 500);
    }

    /**
     * Request reboot for a device (Platform/Admin)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function requestReboot(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|integer',
            'requested_by' => 'nullable|string|max:255',
        ]);

        $result = $this->morphoApiService->requestReboot(
            $request->input('device_id'),
            $request->input('requested_by')
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Reboot request created successfully',
                'data' => $result['data'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to create reboot request',
            'error' => $result['error'] ?? 'Unknown error',
        ], 500);
    }
}

