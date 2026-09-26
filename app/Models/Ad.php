<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_set_id',
        'ad_group_id',
        'organization_id',
        'creative_id',
        'external_id',
        'name',
        'status',
        'title',
        'description',
        'url',
        'image_url',
        'video_url',
        'cta',
        'total_spend',
        'impressions',
        'clicks',
        'ctr',
        'cpc',
        'cpl',
        'conversions',
        'roas',
        'revenue',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'total_spend' => 'float',
            'ctr' => 'float',
            'cpc' => 'float',
            'cpl' => 'float',
            'roas' => 'float',
            'revenue' => 'float',
            'synced_at' => 'datetime',
        ];
    }

    public function adSet(): BelongsTo
    {
        return $this->belongsTo(AdSet::class);
    }

    public function adGroup(): BelongsTo
    {
        return $this->belongsTo(AdGroup::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creative(): BelongsTo
    {
        return $this->belongsTo(Creative::class);
    }
}
