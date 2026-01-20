<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'land_id',
        'driver_id',
        'machine_id',
        'status',
        'start_time',
        'end_time',
        'description',
        'invoice_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function land()
    {
        return $this->belongsTo(Land::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function jobTracking()
    {
        return $this->hasMany(JobTracking::class);
    }
}