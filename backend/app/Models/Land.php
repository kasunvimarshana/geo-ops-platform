<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Land Model
 *
 * Represents a piece of land with GPS coordinates and area information.
 *
 * @property int $id
 * @property int $organization_id
 * @property int $owner_user_id
 * @property string $name
 * @property string|null $description
 * @property array $coordinates
 * @property float|null $area_acres
 * @property float|null $area_hectares
 * @property float|null $area_square_meters
 * @property float|null $center_latitude
 * @property float|null $center_longitude
 * @property string|null $location_address
 * @property string|null $location_district
 * @property string|null $location_province
 * @property string $status
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Land extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'owner_user_id',
        'name',
        'description',
        'coordinates',
        'area_acres',
        'area_hectares',
        'area_square_meters',
        'center_latitude',
        'center_longitude',
        'location_address',
        'location_district',
        'location_province',
        'status',
        'metadata',
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
        'area_acres' => 'decimal:4',
        'area_hectares' => 'decimal:4',
        'area_square_meters' => 'decimal:2',
        'center_latitude' => 'decimal:7',
        'center_longitude' => 'decimal:7',
        'metadata' => 'array',
    ];

    /**
     * Get the organization that owns the land.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who owns this land.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * Get all measurements for this land.
     */
    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }

    /**
     * Get all field jobs for this land.
     */
    public function fieldJobs(): HasMany
    {
        return $this->hasMany(FieldJob::class);
    }

    /**
     * Get the user who created this land.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this land.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active lands.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive lands.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to filter by organization.
     */
    public function scopeByOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope a query to filter by owner.
     */
    public function scopeByOwner($query, int $ownerId)
    {
        return $query->where('owner_user_id', $ownerId);
    }

    /**
     * Scope a query to filter by location.
     */
    public function scopeByLocation($query, ?string $district = null, ?string $province = null)
    {
        if ($district) {
            $query->where('location_district', $district);
        }
        if ($province) {
            $query->where('location_province', $province);
        }
        return $query;
    }

    /**
     * Check if the land is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
