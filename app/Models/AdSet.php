<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'organization_id',
        'external_id',
        'name',
        'status',
        'daily_budget',
        'budget',
        'strategy',
        'audience',
        'placements',
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
        'end_date',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'daily_budget' => 'float',
            'budget' => 'float',
            'total_spend' => 'float',
            'ctr' => 'float',
            'cpc' => 'float',
            'cpl' => 'float',
            'roas' => 'float',
            'revenue' => 'float',
            'start_date' => 'date',
            'end_date' => 'date',
            'placements' => 'array',
            'synced_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }
}
