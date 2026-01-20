<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasOrganization;
use App\Traits\HasAuditFields;

/**
 * Measurement Model
 * 
 * Represents a GPS land measurement with polygon coordinates.
 */
class Measurement extends Model
{
    use HasFactory, SoftDeletes, HasOrganization, HasAuditFields;

    protected $fillable = [
        'organization_id',
        'measured_by',
        'customer_name',
        'customer_phone',
        'location_name',
        'location_address',
        'area_acres',
        'area_hectares',
        'perimeter_meters',
        'center_latitude',
        'center_longitude',
        'measurement_method',
        'measurement_date',
        'notes',
        'status',
        'synced_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'area_acres' => 'decimal:4',
        'area_hectares' => 'decimal:4',
        'perimeter_meters' => 'decimal:2',
        'center_latitude' => 'decimal:8',
        'center_longitude' => 'decimal:8',
        'measurement_date' => 'datetime',
        'synced_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Boot method - apply global scopes
     */
    protected static function boot()
    {
        parent::boot();

        // Apply organization scope
        static::addGlobalScope('organization', function ($query) {
            if (auth()->check()) {
                $query->where('organization_id', auth()->user()->organization_id);
            }
        });
    }

    /**
     * Relationships
     */

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function measuredBy()
    {
        return $this->belongsTo(User::class, 'measured_by');
    }

    public function polygonPoints()
    {
        return $this->hasMany(MeasurementPolygon::class)->orderBy('point_order');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByCustomer($query, string $phone)
    {
        return $query->where('customer_phone', $phone);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('measurement_date', now()->month)
                    ->whereYear('measurement_date', now()->year);
    }

    /**
     * Accessors & Mutators
     */

    public function getAreaDisplayAttribute(): string
    {
        return number_format($this->area_acres, 2) . ' acres (' . 
               number_format($this->area_hectares, 2) . ' ha)';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => '<span class="badge badge-secondary">Draft</span>',
            'completed' => '<span class="badge badge-success">Completed</span>',
            'verified' => '<span class="badge badge-primary">Verified</span>',
            default => '<span class="badge badge-light">Unknown</span>',
        };
    }
}
