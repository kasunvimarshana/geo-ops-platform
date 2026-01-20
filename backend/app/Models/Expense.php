<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'machine_id',
        'driver_id',
        'category',
        'amount',
        'description',
        'receipt_path',
        'expense_date',
        'status',
        'recorded_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
    ];

    const CATEGORY_FUEL = 'fuel';
    const CATEGORY_PARTS = 'parts';
    const CATEGORY_MAINTENANCE = 'maintenance';
    const CATEGORY_LABOR = 'labor';
    const CATEGORY_OTHER = 'other';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
