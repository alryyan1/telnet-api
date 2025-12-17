<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'severity',
        'timestamp',
        'hostname',
        'application',
        'device_id',
        'event_id',
        'message',
        'context',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'timestamp' => 'integer',
        'device_id' => 'integer',
        'event_id' => 'integer',
        'context' => 'array',
    ];
}
