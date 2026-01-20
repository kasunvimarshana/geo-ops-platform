<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'plan_name',
        'plan_type',
        'price',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->ends_at->isFuture();
    }
}
