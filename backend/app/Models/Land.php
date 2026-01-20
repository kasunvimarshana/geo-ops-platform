<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Land extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'polygon',
        'area_acres',
        'area_hectares',
        'measurement_type',
        'location_name',
        'customer_name',
        'customer_phone',
        'measured_by',
        'measured_at',
        'status',
        'sync_status',
        'offline_id',
    ];

    protected $casts = [
        'area_acres' => 'decimal:4',
        'area_hectares' => 'decimal:4',
        'measured_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function measurer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'measured_by');
    }

    public function measurementPoints(): HasMany
    {
        return $this->hasMany(MeasurementPoint::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
