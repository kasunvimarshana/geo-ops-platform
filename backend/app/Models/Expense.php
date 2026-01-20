<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Expense Model
 *
 * Represents an expense for the organization.
 *
 * @property int $id
 * @property int $organization_id
 * @property int|null $job_id
 * @property int|null $driver_id
 * @property string $category
 * @property string $expense_number
 * @property float $amount
 * @property string $currency
 * @property \Carbon\Carbon $expense_date
 * @property string|null $vendor_name
 * @property string $description
 * @property string|null $receipt_path
 * @property array|null $attachments
 * @property bool $is_synced
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $updated_by
 */
class Expense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_id',
        'job_id',
        'driver_id',
        'category',
        'expense_number',
        'amount',
        'currency',
        'expense_date',
        'vendor_name',
        'description',
        'receipt_path',
        'attachments',
        'is_synced',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'attachments' => 'array',
        'is_synced' => 'boolean',
    ];

    /**
     * Get the organization that owns the expense.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the job associated with this expense.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(FieldJob::class, 'job_id');
    }

    /**
     * Get the driver for this expense.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the user who created this expense.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this expense.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to filter by organization.
     */
    public function scopeByOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope a query to filter by job.
     */
    public function scopeByJob($query, int $jobId)
    {
        return $query->where('job_id', $jobId);
    }

    /**
     * Scope a query to filter by driver.
     */
    public function scopeByDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by expense date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include fuel expenses.
     */
    public function scopeFuel($query)
    {
        return $query->where('category', 'fuel');
    }

    /**
     * Scope a query to only include maintenance expenses.
     */
    public function scopeMaintenance($query)
    {
        return $query->where('category', 'maintenance');
    }

    /**
     * Scope a query to only include synced expenses.
     */
    public function scopeSynced($query)
    {
        return $query->where('is_synced', true);
    }

    /**
     * Scope a query to only include unsynced expenses.
     */
    public function scopeUnsynced($query)
    {
        return $query->where('is_synced', false);
    }
}
