<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * FieldJob Model
 *
 * Represents a field work job (plowing, harvesting, etc.).
 *
 * @property int $id
 * @property int $organization_id
 * @property int|null $land_id
 * @property int|null $customer_id
 * @property int|null $driver_id
 * @property string $job_number
 * @property string $status
 * @property string $service_type
 * @property string $customer_name
 * @property string|null $customer_phone
 * @property string|null $customer_address
 * @property array|null $location_coordinates
 * @property float|null $area_acres
 * @property float|null $area_hectares
 * @property float|null $rate_per_unit
 * @property string $rate_unit
 * @property float|null $estimated_amount
 * @property float|null $actual_amount
 * @property \Carbon\Carbon|null $scheduled_date
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $completed_at
 * @property int|null $duration_minutes
 * @property float|null $distance_km
 * @property string|null $notes
 * @property string|null $completion_notes
 * @property array|null $attachments
 * @property bool $is_synced
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class FieldJob extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'field_jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'land_id',
        'customer_id',
        'driver_id',
        'job_number',
        'status',
        'service_type',
        'customer_name',
        'customer_phone',
        'customer_address',
        'location_coordinates',
        'area_acres',
        'area_hectares',
        'rate_per_unit',
        'rate_unit',
        'estimated_amount',
        'actual_amount',
        'scheduled_date',
        'started_at',
        'completed_at',
        'duration_minutes',
        'distance_km',
        'notes',
        'completion_notes',
        'attachments',
        'is_synced',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'location_coordinates' => 'array',
        'area_acres' => 'decimal:4',
        'area_hectares' => 'decimal:4',
        'rate_per_unit' => 'decimal:2',
        'estimated_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'scheduled_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'distance_km' => 'decimal:2',
        'attachments' => 'array',
        'is_synced' => 'boolean',
    ];

    /**
     * Get the organization that owns the job.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the land associated with this job.
     */
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    /**
     * Get the customer for this job.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the driver assigned to this job.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get all tracking logs for this job.
     */
    public function trackingLogs(): HasMany
    {
        return $this->hasMany(TrackingLog::class, 'job_id');
    }

    /**
     * Get the invoice for this job.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'job_id');
    }

    /**
     * Get all expenses for this job.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'job_id');
    }

    /**
     * Get the user who created this job.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this job.
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
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by driver.
     */
    public function scopeByDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Scope a query to filter by customer.
     */
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to filter by service type.
     */
    public function scopeByServiceType($query, string $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope a query to only include pending jobs.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include assigned jobs.
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    /**
     * Scope a query to only include in-progress jobs.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed jobs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled jobs.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to filter by scheduled date range.
     */
    public function scopeScheduledBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('scheduled_date', [$startDate, $endDate]);
    }

    /**
     * Check if the job is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the job is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the job is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }
}
