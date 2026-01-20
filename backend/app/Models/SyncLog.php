<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user_id',
        'entity_type',
        'entity_id',
        'offline_id',
        'action',
        'sync_status',
        'conflict_data',
        'error_message',
        'synced_at',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'conflict_data' => 'array',
        'synced_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
