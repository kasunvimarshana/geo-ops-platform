<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'organization_id',
        'user_id',
        'boundary',
        'area',
        'perimeter',
        'crop_type',
        'notes',
        'measurement_type',
        'measured_at',
    ];

    protected $casts = [
        'boundary' => 'array',
        'area' => 'decimal:2',
        'perimeter' => 'decimal:2',
        'measured_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
