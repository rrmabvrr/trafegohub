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
        'creative_id',
        'external_id',
        'name',
        'status',
        'total_spend',
        'impressions',
        'clicks',
        'ctr',
        'cpc',
        'cpl',
        'conversions',
        'roas',
        'revenue',
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
        ];
    }

    public function adSet(): BelongsTo
    {
        return $this->belongsTo(AdSet::class);
    }

    public function creative(): BelongsTo
    {
        return $this->belongsTo(Creative::class);
    }
}
