<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'driver_id',
        'job_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'heading',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function scopeForDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForJob($query, int $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }
}
