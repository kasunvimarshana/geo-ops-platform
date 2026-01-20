<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class LandPlot extends Model
{
    use SoftDeletes, HasSpatial;

    protected $fillable = [
        'organization_id',
        'user_id',
        'name',
        'description',
        'area_acres',
        'area_hectares',
        'area_square_meters',
        'perimeter_meters',
        'coordinates',
        'center_latitude',
        'center_longitude',
        'location',
        'measurement_method',
        'accuracy_meters',
        'measured_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'area_acres' => 'decimal:4',
            'area_hectares' => 'decimal:4',
            'area_square_meters' => 'decimal:2',
            'perimeter_meters' => 'decimal:2',
            'coordinates' => 'array',
            'center_latitude' => 'decimal:8',
            'center_longitude' => 'decimal:8',
            'location' => Geometry::class,
            'accuracy_meters' => 'decimal:2',
            'measured_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fieldJobs(): HasMany
    {
        return $this->hasMany(FieldJob::class);
    }

    public function scopeOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
