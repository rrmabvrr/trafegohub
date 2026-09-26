<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'organization_id',
        'client_id',
        'integration_id',
        'connection_id',
        'ad_account_id',
        'platform_id',
        'external_id',
        'platform',
        'name',
        'status',
        'objective',
        'daily_budget',
        'currency',
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
        'target_audience',
    ];

    protected function casts(): array
    {
        return [
            'status' => CampaignStatus::class,
            'daily_budget' => 'float',
            'total_spend' => 'float',
            'ctr' => 'float',
            'cpc' => 'float',
            'cpl' => 'float',
            'roas' => 'float',
            'revenue' => 'float',
            'start_date' => 'date',
            'end_date' => 'date',
            'synced_at' => 'datetime',
        ];
    }

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

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }

    public function adAccount(): BelongsTo
    {
        return $this->belongsTo(AdAccount::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function account(): BelongsTo
    {
        return $this->integration();
    }

    public function creatives(): HasMany
    {
        return $this->hasMany(Creative::class);
    }

    public function adSets(): HasMany
    {
        return $this->hasMany(AdSet::class);
    }

    public function ads(): HasManyThrough
    {
        return $this->hasManyThrough(Ad::class, AdSet::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function metricSnapshots(): HasMany
    {
        return $this->hasMany(CampaignMetricSnapshot::class);
    }

    public function metrics(): HasMany
    {
        return $this->metricSnapshots();
    }
}
