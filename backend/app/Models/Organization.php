<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'contact_number',
        'email',
        'subscription_tier',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function lands()
    {
        return $this->hasMany(Land::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function machines()
    {
        return $this->hasMany(Machine::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}