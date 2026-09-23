<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'organization_id',
        'name',
        'platform_filter',
        'metric',
        'condition',
        'threshold',
        'time_frame',
        'action',
        'action_value',
        'is_enabled',
        'trigger_count',
        'last_triggered_at',
    ];

    protected $casts = [
        'threshold' => 'float',
        'action_value' => 'float',
        'is_enabled' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AutomationLog::class);
    }
}
