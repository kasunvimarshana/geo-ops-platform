<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class LandMeasurement extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'coordinates',
        'area_acres',
        'area_hectares',
        'measured_by',
        'measured_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'area_acres' => 'decimal:4',
        'area_hectares' => 'decimal:4',
        'measured_at' => 'datetime',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function booted()
    {
        // Apply organization scope automatically
        static::addGlobalScope('organization', function ($query) {
            if (auth()->check() && !auth()->user()->isAdmin()) {
                $query->where('organization_id', auth()->user()->organization_id);
            }
        });
    }

    /**
     * Get the organization that owns the measurement.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who measured the land.
     */
    public function measuredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'measured_by');
    }

    /**
     * Get the jobs for this measurement.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Set coordinates from array of lat/lng points.
     */
    public function setCoordinatesFromArray(array $points): void
    {
        // Convert array to WKT (Well-Known Text) format for polygon
        $coordinates = collect($points)
            ->map(fn($point) => "{$point['longitude']} {$point['latitude']}")
            ->join(',');
        
        // Close the polygon (first point = last point)
        $firstPoint = $points[0];
        $coordinates .= ",{$firstPoint['longitude']} {$firstPoint['latitude']}";
        
        $wkt = "POLYGON(($coordinates))";
        
        if (config('database.default') === 'mysql') {
            $this->attributes['coordinates'] = DB::raw("ST_GeomFromText('$wkt')");
        } elseif (config('database.default') === 'pgsql') {
            $this->attributes['coordinates'] = DB::raw("ST_GeomFromText('$wkt', 4326)");
        }
    }

    /**
     * Get coordinates as GeoJSON.
     */
    public function getCoordinatesAsGeoJson(): array
    {
        if (config('database.default') === 'mysql') {
            $result = DB::selectOne(
                "SELECT ST_AsGeoJSON(coordinates) as geojson FROM land_measurements WHERE id = ?",
                [$this->id]
            );
        } elseif (config('database.default') === 'pgsql') {
            $result = DB::selectOne(
                "SELECT ST_AsGeoJSON(coordinates) as geojson FROM land_measurements WHERE id = ?",
                [$this->id]
            );
        }
        
        return json_decode($result->geojson ?? '{}', true);
    }

    /**
     * Calculate area from coordinates (in square meters).
     */
    public static function calculateArea(array $coordinates): float
    {
        $earthRadius = 6378137; // meters
        $area = 0;
        $count = count($coordinates);

        for ($i = 0; $i < $count; $i++) {
            $j = ($i + 1) % $count;
            $xi = deg2rad($coordinates[$i]['longitude']);
            $yi = deg2rad($coordinates[$i]['latitude']);
            $xj = deg2rad($coordinates[$j]['longitude']);
            $yj = deg2rad($coordinates[$j]['latitude']);

            $area += ($xj - $xi) * (2 + sin($yi) + sin($yj));
        }

        return abs($area * $earthRadius * $earthRadius / 2);
    }

    /**
     * Convert square meters to acres.
     */
    public static function metersToAcres(float $squareMeters): float
    {
        return $squareMeters / 4046.86;
    }

    /**
     * Convert square meters to hectares.
     */
    public static function metersToHectares(float $squareMeters): float
    {
        return $squareMeters / 10000;
    }
}
