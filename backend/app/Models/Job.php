<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'organization_id',
        'field_id',
        'created_by',
        'assigned_to',
        'status',
        'priority',
        'due_date',
        'started_at',
        'completed_at',
        'location',
    ];

    protected $casts = [
        'location' => 'array',
        'due_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
