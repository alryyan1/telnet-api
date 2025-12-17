<?php

namespace App\Http\Controllers;

use App\Models\DeviceStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceStatusController extends Controller
{
    /**
     * Get device statuses by device_id (serial_number)
     *
     * @param Request $request
     * @param string|int $device_id - Can be serial_number (string) or device_id (integer)
     * @return JsonResponse
     */
    public function getByDeviceId(Request $request, $device_id): JsonResponse
    {
        // Validate request parameters
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:1000',
            'offset' => 'nullable|integer|min:0',
            'order_by' => 'nullable|in:timestamp,created_at',
            'order_direction' => 'nullable|in:asc,desc',
            'start_timestamp' => 'nullable|integer',
            'end_timestamp' => 'nullable|integer',
        ]);

        $limit = $request->input('limit', 100);
        $offset = $request->input('offset', 0);
        $orderBy = $request->input('order_by', 'timestamp');
        $orderDirection = $request->input('order_direction', 'desc');
        $startTimestamp = $request->input('start_timestamp');
        $endTimestamp = $request->input('end_timestamp');

        // Convert serial_number to integer if it's numeric, otherwise use as-is
        // In device_statuses table, device_id stores the serial_number as integer
        $deviceIdValue = is_numeric($device_id) ? (int)$device_id : $device_id;

        // Base query for device statuses
        $query = DeviceStatus::where('device_id', $deviceIdValue);

        // Optional timestamp range filtering
        if (!is_null($startTimestamp)) {
            $query->where('timestamp', '>=', $startTimestamp);
        }

        if (!is_null($endTimestamp)) {
            $query->where('timestamp', '<=', $endTimestamp);
        }

        // Apply ordering and pagination
        $statuses = $query
            ->orderBy($orderBy, $orderDirection)
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Total count for meta (respecting same filters but without pagination)
        $totalQuery = DeviceStatus::where('device_id', $deviceIdValue);

        if (!is_null($startTimestamp)) {
            $totalQuery->where('timestamp', '>=', $startTimestamp);
        }

        if (!is_null($endTimestamp)) {
            $totalQuery->where('timestamp', '<=', $endTimestamp);
        }

        $total = $totalQuery->count();

        return response()->json([
            'success' => true,
            'data' => $statuses,
            'meta' => [
                'device_id' => $deviceIdValue,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'count' => $statuses->count(),
            ],
        ], 200);
    }

    /**
     * Get latest device status by device_id (serial_number)
     *
     * @param string|int $device_id - Can be serial_number (string) or device_id (integer)
     * @return JsonResponse
     */
    public function getLatestByDeviceId($device_id): JsonResponse
    {
        // Convert serial_number to integer if it's numeric, otherwise use as-is
        $deviceIdValue = is_numeric($device_id) ? (int)$device_id : $device_id;

        $status = DeviceStatus::where('device_id', $deviceIdValue)
            ->orderBy('timestamp', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'No device status found for the given device_id',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $status,
        ], 200);
    }
}

