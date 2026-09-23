<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'legal_name',
        'document',
        'email',
        'phone',
        'whatsapp',
        'address',
        'status',
        'notes',
        'external_reference',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }

    public function campaigns(): HasManyThrough
    {
        return $this->hasManyThrough(Campaign::class, Workspace::class);
    }

    public function advertisingAccounts(): HasManyThrough
    {
        return $this->hasManyThrough(Integration::class, Workspace::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
