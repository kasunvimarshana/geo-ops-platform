<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'driver_id',
        'machine_id',
        'amount',
        'description',
        'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}