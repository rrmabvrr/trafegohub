<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'integration_id',
        'platform',
        'name',
        'status',
        'objective',
        'daily_budget',
        'total_spend',
        'impressions',
        'clicks',
        'ctr',
        'cpc',
        'cpl',
        'conversions',
        'roas',
        'revenue',
        'start_date',
        'target_audience',
    ];

    protected $casts = [
        'daily_budget' => 'float',
        'total_spend' => 'float',
        'ctr' => 'float',
        'cpc' => 'float',
        'cpl' => 'float',
        'roas' => 'float',
        'revenue' => 'float',
        'start_date' => 'date',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function creatives(): HasMany
    {
        return $this->hasMany(Creative::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
