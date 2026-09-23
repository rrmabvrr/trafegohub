<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Creative extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'name',
        'type',
        'platform',
        'thumbnail_url',
        'ctr',
        'hook_rate',
        'roas',
        'spend',
        'conversions',
        'fatigue_level',
        'fatigue_reason',
        'frequency',
    ];

    protected $casts = [
        'ctr' => 'float',
        'hook_rate' => 'float',
        'roas' => 'float',
        'spend' => 'float',
        'frequency' => 'float',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }
}
