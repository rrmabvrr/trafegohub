<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Metric extends Model
{
    use HasFactory;

    protected $table = 'metrics';

    protected $fillable = [
        'organization_id',
        'workspace_id',
        'client_id',
        'integration_id',
        'connection_id',
        'ad_account_id',
        'platform_id',
        'campaign_id',
        'ad_set_id',
        'ad_id',
        'creative_id',
        'platform',
        'account_name',
        'date',
        'period',
        'impressions',
        'reach',
        'frequency',
        'clicks',
        'link_clicks',
        'spend',
        'cpm',
        'cpc',
        'ctr',
        'leads',
        'conversions',
        'revenue',
        'roas',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'impressions' => 'integer',
            'reach' => 'integer',
            'frequency' => 'float',
            'clicks' => 'integer',
            'link_clicks' => 'integer',
            'spend' => 'float',
            'cpm' => 'float',
            'cpc' => 'float',
            'ctr' => 'float',
            'leads' => 'integer',
            'conversions' => 'integer',
            'revenue' => 'float',
            'roas' => 'float',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
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

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function adSet(): BelongsTo
    {
        return $this->belongsTo(AdSet::class);
    }

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    public function creative(): BelongsTo
    {
        return $this->belongsTo(Creative::class);
    }
}
