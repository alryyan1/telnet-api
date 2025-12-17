<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'device_id',
        'client_id',
        'firmware_version',
        'ip_address',
        'timestamp',
        'gps_latitude',
        'gps_longitude',
        'gps_altitude',
        'gps_accuracy',
        'rssi',
        'batterie_level',
        'temperature',
        'humidity',
        'mean_vibration',
        'light',
        'status',
        'nbrfid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'device_id' => 'integer',
        'client_id' => 'integer',
        'timestamp' => 'integer',
        'gps_latitude' => 'decimal:7',
        'gps_longitude' => 'decimal:7',
        'gps_altitude' => 'decimal:2',
        'gps_accuracy' => 'decimal:2',
        'rssi' => 'integer',
        'batterie_level' => 'integer',
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'mean_vibration' => 'decimal:2',
        'light' => 'decimal:2',
        'nbrfid' => 'integer',
    ];
}
