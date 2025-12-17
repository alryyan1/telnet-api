<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceConfig extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'device_id',
        'CAN_id',
        'RFID_enable',
        'SD_enable',
        'client_id',
        'config_timestamp',
        'configured_by',
        'debug_enable',
        'endpoint_URL',
        'frequency',
        'mode',
        'sf',
        'status',
        'thresholds',
        'timestamp',
        'txp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'device_id' => 'integer',
        'CAN_id' => 'integer',
        'RFID_enable' => 'boolean',
        'SD_enable' => 'boolean',
        'client_id' => 'integer',
        'config_timestamp' => 'integer',
        'debug_enable' => 'boolean',
        'frequency' => 'integer',
        'mode' => 'integer',
        'status' => 'boolean',
        'thresholds' => 'array',
        'timestamp' => 'integer',
        'txp' => 'integer',
    ];

    /**
     * Get the latest configuration for a device
     *
     * @param int $deviceId
     * @return DeviceConfig|null
     */
    public static function getLatestForDevice(int $deviceId): ?self
    {
        return self::where('device_id', $deviceId)
            ->orderBy('config_timestamp', 'desc')
            ->first();
    }
}
