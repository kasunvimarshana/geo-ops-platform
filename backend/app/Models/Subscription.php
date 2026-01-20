<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'package',
        'status',
        'starts_at',
        'ends_at',
        'amount',
        'payment_method',
        'payment_reference',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    const PACKAGE_FREE = 'free';
    const PACKAGE_BASIC = 'basic';
    const PACKAGE_PRO = 'pro';

    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'cancelled';

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('ends_at', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->ends_at->isFuture();
    }
}
