<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SubscriptionPackage Model
 *
 * Represents a subscription package with limits and pricing.
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property int $max_measurements
 * @property int $max_drivers
 * @property int $max_jobs
 * @property int $max_lands
 * @property int $max_storage_mb
 * @property float $price_monthly
 * @property float|null $price_yearly
 * @property array|null $features
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SubscriptionPackage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'max_measurements',
        'max_drivers',
        'max_jobs',
        'max_lands',
        'max_storage_mb',
        'price_monthly',
        'price_yearly',
        'features',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_measurements' => 'integer',
        'max_drivers' => 'integer',
        'max_jobs' => 'integer',
        'max_lands' => 'integer',
        'max_storage_mb' => 'integer',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active packages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive packages.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to filter by name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Scope a query to order by price.
     */
    public function scopeOrderByPrice($query, string $direction = 'asc')
    {
        return $query->orderBy('price_monthly', $direction);
    }

    /**
     * Check if the package is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the package has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        return $this->features && in_array($feature, $this->features);
    }

    /**
     * Get the yearly savings amount.
     */
    public function getYearlySavingsAttribute(): ?float
    {
        if (!$this->price_yearly) {
            return null;
        }
        
        $monthlyTotal = $this->price_monthly * 12;
        return round($monthlyTotal - $this->price_yearly, 2);
    }

    /**
     * Get the yearly savings percentage.
     */
    public function getYearlySavingsPercentageAttribute(): ?float
    {
        if (!$this->price_yearly) {
            return null;
        }
        
        $monthlyTotal = $this->price_monthly * 12;
        if ($monthlyTotal == 0) {
            return null;
        }
        
        return round((($monthlyTotal - $this->price_yearly) / $monthlyTotal) * 100, 2);
    }
}
