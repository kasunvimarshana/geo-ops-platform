<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Organization Model
 *
 * Represents an organization/company in the system.
 * Organizations contain users, lands, jobs, measurements, etc.
 *
 * @property int $id
 * @property string $name
 * @property string|null $contact_name
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $address
 * @property string $package_tier
 * @property \Carbon\Carbon|null $package_expires_at
 * @property bool $is_active
 * @property array|null $settings
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Organization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address',
        'package_tier',
        'package_expires_at',
        'is_active',
        'settings',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'package_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get all users belonging to this organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all lands belonging to this organization.
     */
    public function lands(): HasMany
    {
        return $this->hasMany(Land::class);
    }

    /**
     * Get all measurements belonging to this organization.
     */
    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }

    /**
     * Get all field jobs belonging to this organization.
     */
    public function fieldJobs(): HasMany
    {
        return $this->hasMany(FieldJob::class);
    }

    /**
     * Get all tracking logs belonging to this organization.
     */
    public function trackingLogs(): HasMany
    {
        return $this->hasMany(TrackingLog::class);
    }

    /**
     * Get all invoices belonging to this organization.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get all payments belonging to this organization.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all expenses belonging to this organization.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get all audit logs belonging to this organization.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the user who created this organization.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this organization.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active organizations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive organizations.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to filter by package tier.
     */
    public function scopeByPackageTier($query, string $tier)
    {
        return $query->where('package_tier', $tier);
    }

    /**
     * Scope a query to only include organizations with expired packages.
     */
    public function scopeExpiredPackages($query)
    {
        return $query->where('package_expires_at', '<', now())
            ->whereNotNull('package_expires_at');
    }

    /**
     * Scope a query to only include organizations with valid packages.
     */
    public function scopeValidPackages($query)
    {
        return $query->where(function ($query) {
            $query->where('package_expires_at', '>=', now())
                ->orWhereNull('package_expires_at');
        });
    }

    /**
     * Check if the organization's package has expired.
     */
    public function hasExpiredPackage(): bool
    {
        return $this->package_expires_at && $this->package_expires_at->isPast();
    }

    /**
     * Check if the organization has a specific package tier.
     */
    public function hasPackageTier(string $tier): bool
    {
        return $this->package_tier === $tier;
    }
}
