<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
