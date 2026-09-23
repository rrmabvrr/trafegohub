<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignMetricSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'workspace_id',
        'campaign_id',
        'integration_id',
        'date',
        'spend',
        'impressions',
        'reach',
        'clicks',
        'leads',
        'conversions',
        'revenue',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'spend' => 'float',
            'revenue' => 'float',
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

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
