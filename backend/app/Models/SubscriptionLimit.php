<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'measurements_count',
        'measurements_limit',
        'drivers_count',
        'drivers_limit',
        'exports_count',
        'exports_limit',
        'reset_at',
    ];

    protected $casts = [
        'measurements_count' => 'integer',
        'measurements_limit' => 'integer',
        'drivers_count' => 'integer',
        'drivers_limit' => 'integer',
        'exports_count' => 'integer',
        'exports_limit' => 'integer',
        'reset_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
