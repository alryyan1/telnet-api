<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RebootRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'device_id',
        'requested_by',
        'executed',
        'executed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'device_id' => 'integer',
        'executed' => 'boolean',
        'executed_at' => 'datetime',
    ];

    /**
     * Get pending reboot request for a device
     *
     * @param int $deviceId
     * @return RebootRequest|null
     */
    public static function getPendingForDevice(int $deviceId): ?self
    {
        return self::where('device_id', $deviceId)
            ->where('executed', false)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
