<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'organization_id',
        'campaign_id',
        'name',
        'email',
        'phone',
        'platform',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'cpl',
        'deal_value',
        'status',
        'city',
    ];

    protected $casts = [
        'cpl' => 'float',
        'deal_value' => 'float',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
