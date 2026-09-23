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
        'client_id',
        'currency',
        'timezone',
        'platform',
        'name',
        'account_id',
        'external_account_id',
        'ad_account_name',
        'status',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'expires_at',
        'scopes',
        'connected_user_id',
        'connected_at',
        'webhook_url',
        'last_synced_at',
        'last_error',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'expires_at' => 'datetime',
        'scopes' => 'array',
        'connected_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function connectedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_user_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
