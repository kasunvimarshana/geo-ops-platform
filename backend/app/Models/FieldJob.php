<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldJob extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'land_plot_id',
        'driver_id',
        'created_by',
        'customer_name',
        'customer_phone',
        'customer_address',
        'job_type',
        'status',
        'priority',
        'scheduled_date',
        'start_time',
        'end_time',
        'duration_hours',
        'rate_per_unit',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'duration_hours' => 'decimal:2',
            'rate_per_unit' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function landPlot(): BelongsTo
    {
        return $this->belongsTo(LandPlot::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function gpsTracking(): HasMany
    {
        return $this->hasMany(GpsTracking::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function scopeOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }
}
