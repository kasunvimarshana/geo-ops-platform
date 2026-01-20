<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'land_id',
        'latitude',
        'longitude',
        'altitude',
        'accuracy',
        'sequence',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'altitude' => 'decimal:2',
        'accuracy' => 'decimal:2',
        'sequence' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }
}
