<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Land extends Model
{
    use HasFactory;

    protected $table = 'lands';

    protected $fillable = [
        'organization_id',
        'user_id',
        'name',
        'area',
        'coordinates',
        'measurement_history',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'measurement_history' => 'array',
    ];

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