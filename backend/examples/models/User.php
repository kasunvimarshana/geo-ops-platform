<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * User Model
 * 
 * Represents application users with JWT authentication.
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'role_id',
        'name',
        'email',
        'phone',
        'password',
        'profile_photo',
        'language',
        'status',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'settings',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * JWT Methods
     */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'organization_id' => $this->organization_id,
            'role' => $this->role->name,
        ];
    }

    /**
     * Relationships
     */

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function measurements()
    {
        return $this->hasMany(Measurement::class, 'measured_by');
    }

    public function assignedJobs()
    {
        return $this->hasMany(JobAssignment::class, 'driver_id');
    }

    public function gpsTracking()
    {
        return $this->hasMany(GpsTracking::class);
    }

    /**
     * Permission Checks
     */

    public function hasPermission(string $permission): bool
    {
        return $this->role->permissions()
            ->where('name', $permission)
            ->exists();
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role->name === $roleName;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isDriver(): bool
    {
        return $this->hasRole('driver');
    }

    /**
     * Scopes
     */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, string $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Accessors
     */

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if ($this->profile_photo) {
            return url('storage/' . $this->profile_photo);
        }
        
        return null;
    }
}
