<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'organization_id',
        'subscription_id',
        'job_id',
        'type',
        'description',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'payment_method',
        'issued_at',
        'due_date',
        'paid_at',
        'items',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'items' => 'array',
        'issued_at' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && 
               $this->due_date && 
               $this->due_date->isPast();
    }
}
