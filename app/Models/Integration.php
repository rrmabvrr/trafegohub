<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'organization_id',
        'platform',
        'name',
        'account_id',
        'ad_account_name',
        'status',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'webhook_url',
        'last_synced_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_synced_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
