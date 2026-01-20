<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TrackingLog Model
 *
 * Represents a GPS tracking log entry for a user during a job.
 *
 * @property int $id
 * @property int $organization_id
 * @property int $user_id
 * @property int|null $job_id
 * @property float $latitude
 * @property float $longitude
 * @property float|null $accuracy_meters
 * @property float|null $altitude_meters
 * @property float|null $speed_mps
 * @property float|null $heading_degrees
 * @property \Carbon\Carbon $recorded_at
 * @property string|null $device_id
 * @property string|null $platform
 * @property array|null $metadata
 * @property bool $is_synced
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class TrackingLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'user_id',
        'job_id',
        'latitude',
        'longitude',
        'accuracy_meters',
        'altitude_meters',
        'speed_mps',
        'heading_degrees',
        'recorded_at',
        'device_id',
        'platform',
        'metadata',
        'is_synced',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'accuracy_meters' => 'decimal:2',
        'altitude_meters' => 'decimal:2',
        'speed_mps' => 'decimal:2',
        'heading_degrees' => 'decimal:2',
        'recorded_at' => 'datetime',
        'metadata' => 'array',
        'is_synced' => 'boolean',
    ];

    /**
     * Get the organization that owns the tracking log.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user that this tracking log belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job that this tracking log belongs to.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(FieldJob::class, 'job_id');
    }

    /**
     * Scope a query to filter by organization.
     */
    public function scopeByOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by job.
     */
    public function scopeByJob($query, int $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope a query to only include synced logs.
     */
    public function scopeSynced($query)
    {
        return $query->where('is_synced', true);
    }

    /**
     * Scope a query to only include unsynced logs.
     */
    public function scopeUnsynced($query)
    {
        return $query->where('is_synced', false);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('recorded_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by platform.
     */
    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Get the speed in km/h.
     */
    public function getSpeedKmhAttribute(): ?float
    {
        return $this->speed_mps ? round($this->speed_mps * 3.6, 2) : null;
    }
}
