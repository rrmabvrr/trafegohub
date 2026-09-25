<?php

namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'workspace_id',
        'organization_id',
        'campaign_id',
        'ad_id',
        'ad_set_id',
        'name',
        'phone',
        'email',
        'source',
        'origin',
        'platform',
        'channel',
        'campaign_name',
        'ad_name',
        'form_id',
        'external_lead_id',
        'status',
        'lead_date',
        'observations',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'lead_date' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

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

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    public function adSet(): BelongsTo
    {
        return $this->belongsTo(AdSet::class);
    }
}
