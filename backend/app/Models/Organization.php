<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
        'subscription_package',
        'subscription_expires_at',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
        'subscription_expires_at' => 'datetime',
    ];

    /**
     * Get the owner of the organization.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the users for the organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the subscriptions for the organization.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the current active subscription.
     */
    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latest('starts_at');
    }

    /**
     * Check if subscription is active.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_expires_at && 
               $this->subscription_expires_at->isFuture();
    }

    /**
     * Get subscription limits based on package.
     */
    public function getSubscriptionLimits(): array
    {
        $limits = config('geo-ops.subscription_limits');
        
        return $limits[$this->subscription_package] ?? $limits['free'];
    }
}
