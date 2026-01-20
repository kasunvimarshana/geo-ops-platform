<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * User Model
 *
 * Represents a user in the system with various roles.
 *
 * @property int $id
 * @property int $organization_id
 * @property string $role
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property bool $is_active
 * @property \Carbon\Carbon|null $last_login_at
 * @property \Carbon\Carbon|null $email_verified_at
 * @property array|null $settings
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'role',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'is_active',
        'last_login_at',
        'email_verified_at',
        'settings',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
        ];
    }

    /**
     * Get the organization that owns the user.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get all lands owned by this user.
     */
    public function ownedLands(): HasMany
    {
        return $this->hasMany(Land::class, 'owner_user_id');
    }

    /**
     * Get all measurements created by this user.
     */
    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }

    /**
     * Get all field jobs where this user is the customer.
     */
    public function customerJobs(): HasMany
    {
        return $this->hasMany(FieldJob::class, 'customer_id');
    }

    /**
     * Get all field jobs where this user is the driver.
     */
    public function driverJobs(): HasMany
    {
        return $this->hasMany(FieldJob::class, 'driver_id');
    }

    /**
     * Get all tracking logs for this user.
     */
    public function trackingLogs(): HasMany
    {
        return $this->hasMany(TrackingLog::class);
    }

    /**
     * Get all invoices where this user is the customer.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    /**
     * Get all payments where this user is the customer.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }

    /**
     * Get all expenses where this user is the driver.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'driver_id');
    }

    /**
     * Get all audit logs for this user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the user who created this user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this user.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to filter by organization.
     */
    public function scopeByOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to only include drivers.
     */
    public function scopeDrivers($query)
    {
        return $query->where('role', 'driver');
    }

    /**
     * Scope a query to only include admins.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include brokers.
     */
    public function scopeBrokers($query)
    {
        return $query->where('role', 'broker');
    }

    /**
     * Scope a query to only include accountants.
     */
    public function scopeAccountants($query)
    {
        return $query->where('role', 'accountant');
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a driver.
     */
    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    /**
     * Check if the user is an owner.
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check if the user is a broker.
     */
    public function isBroker(): bool
    {
        return $this->role === 'broker';
    }

    /**
     * Check if the user is an accountant.
     */
    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'organization_id' => $this->organization_id,
            'role' => $this->role,
        ];
    }
}
