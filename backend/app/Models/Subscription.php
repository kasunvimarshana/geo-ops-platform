<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'tier',
        'start_date',
        'end_date',
        'status',
        'usage_limit',
        'usage_count',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function isExpired()
    {
        return $this->end_date <= now();
    }

    public function renew($newEndDate)
    {
        $this->end_date = $newEndDate;
        $this->status = 'active';
        $this->save();
    }
}