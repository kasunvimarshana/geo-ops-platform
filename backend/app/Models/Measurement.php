<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Measurement Model
 *
 * Represents a land measurement with GPS coordinates and calculated areas.
 *
 * @property int $id
 * @property int $land_id
 * @property int $user_id
 * @property int $organization_id
 * @property string $type
 * @property array $coordinates
 * @property float $area_square_meters
 * @property float $area_acres
 * @property float $area_hectares
 * @property float|null $perimeter_meters
 * @property array|null $center_point
 * @property int $point_count
 * @property float|null $accuracy_meters
 * @property \Carbon\Carbon $measurement_started_at
 * @property \Carbon\Carbon $measurement_completed_at
 * @property int|null $duration_seconds
 * @property string|null $notes
 * @property bool $is_synced
 * @property string|null $device_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Measurement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'land_id',
        'user_id',
        'organization_id',
        'type',
        'coordinates',
        'area_square_meters',
        'area_acres',
        'area_hectares',
        'perimeter_meters',
        'center_point',
        'point_count',
        'accuracy_meters',
        'measurement_started_at',
        'measurement_completed_at',
        'duration_seconds',
        'notes',
        'is_synced',
        'device_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'coordinates' => 'array',
        'area_square_meters' => 'decimal:2',
        'area_acres' => 'decimal:4',
        'area_hectares' => 'decimal:4',
        'perimeter_meters' => 'decimal:2',
        'center_point' => 'array',
        'accuracy_meters' => 'decimal:2',
        'measurement_started_at' => 'datetime',
        'measurement_completed_at' => 'datetime',
        'is_synced' => 'boolean',
    ];

    /**
     * Get the land that this measurement belongs to.
     */
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    /**
     * Get the user who created this measurement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organization that this measurement belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who created this measurement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this measurement.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by organization.
     */
    public function scopeByOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope a query to filter by land.
     */
    public function scopeByLand($query, int $landId)
    {
        return $query->where('land_id', $landId);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include synced measurements.
     */
    public function scopeSynced($query)
    {
        return $query->where('is_synced', true);
    }

    /**
     * Scope a query to only include unsynced measurements.
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
        return $query->whereBetween('measurement_started_at', [$startDate, $endDate]);
    }

    /**
     * Get the duration in minutes.
     */
    public function getDurationMinutesAttribute(): ?float
    {
        return $this->duration_seconds ? round($this->duration_seconds / 60, 2) : null;
    }
}
