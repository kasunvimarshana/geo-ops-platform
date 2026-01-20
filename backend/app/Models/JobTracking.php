<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTracking extends Model
{
    use HasFactory;

    protected $table = 'job_tracking';

    protected $fillable = [
        'job_id',
        'latitude',
        'longitude',
        'timestamp',
        'status',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}