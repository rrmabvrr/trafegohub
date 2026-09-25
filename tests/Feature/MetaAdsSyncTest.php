<?php

namespace Tests\Feature;

use App\Jobs\SyncPlatformMetricsJob;
use App\Models\Ad;
use App\Models\AdSet;
use App\Models\Campaign;
use App\Models\Integration;
use App\Models\Workspace;
use App\Services\AdvertisingPlatformManager;
use App\Services\MetaAdsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MetaAdsSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_the_meta_campaign_hierarchy_idempotently(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://graph.facebook.com/v21.0/act_123/campaigns*' => Http::response([
                'data' => [[
                    'id' => 'campaign-1',
                    'name' => 'Meta Sales',
                    'status' => 'ACTIVE',
                    'objective' => 'OUTCOME_SALES',
                    'daily_budget' => '5000',
                    'insights' => ['data' => [[
                        'spend' => '125.50',
                        'impressions' => '10000',
                        'clicks' => '400',
                        'ctr' => '4.00',
                        'cpc' => '0.31',
                        'conversions' => '10',
                        'purchase_roas' => [['value' => '3.20']],
                    ]]],
                    'adsets' => ['data' => [[
                        'id' => 'adset-1',
                        'name' => 'Prospecting',
                        'status' => 'ACTIVE',
                        'daily_budget' => '2500',
                        'ads' => ['data' => [[
                            'id' => 'ad-1',
                            'name' => 'Video ad',
                            'status' => 'ACTIVE',
                        ]]],
                    ]]],
                ]],
            ]),
        ]);

        $integration = Integration::create([
            'workspace_id' => Workspace::create([
                'name' => 'Client workspace',
                'client_name' => 'Client',
            ])->id,
            'platform' => 'meta',
            'name' => 'Meta Ads',
            'account_id' => 'act_123',
            'ad_account_name' => 'Account',
            'status' => 'CONNECTED',
            'access_token' => 'test-token',
        ]);

        $this->assertSame('CONNECTED', $integration->fresh()->status);
        $this->assertSame('test-token', $integration->fresh()->access_token);

        $job = new SyncPlatformMetricsJob($integration->id);
        $job->handle(app(AdvertisingPlatformManager::class));
        $job->handle(app(AdvertisingPlatformManager::class));

        Http::assertSentCount(2);
        $this->assertDatabaseCount('campaigns', 1);
        $this->assertDatabaseCount('ad_sets', 1);
        $this->assertDatabaseCount('ads', 1);
        $this->assertDatabaseHas('campaigns', [
            'external_id' => 'campaign-1',
            'daily_budget' => 50,
            'total_spend' => 125.5,
        ]);
        $this->assertSame('CONNECTED', $integration->fresh()->status);
    }

    public function test_it_does_not_call_a_platform_without_an_access_token(): void
    {
        Http::preventStrayRequests();

        $integration = Integration::create([
            'workspace_id' => Workspace::create([
                'name' => 'Client workspace',
                'client_name' => 'Client',
            ])->id,
            'platform' => 'meta',
            'name' => 'Meta Ads',
            'account_id' => '123',
            'ad_account_name' => 'Account',
            'status' => 'CONNECTED',
        ]);

        (new SyncPlatformMetricsJob($integration->id))
            ->handle(app(AdvertisingPlatformManager::class));

        Http::assertNothingSent();
        $this->assertSame('ERROR', $integration->fresh()->status);
        $this->assertSame(0, Campaign::count());
        $this->assertSame(0, AdSet::count());
        $this->assertSame(0, Ad::count());
    }

    public function test_it_supports_unified_ad_set_fields_for_all_platforms(): void
    {
        $campaign = Campaign::create([
            'workspace_id' => Workspace::create([
                'name' => 'Client workspace',
                'client_name' => 'Client',
            ])->id,
            'platform' => 'google',
            'name' => 'Campanha teste',
            'status' => 'ACTIVE',
            'objective' => 'LEADS',
            'daily_budget' => 120,
            'external_id' => 'campaign-999',
        ]);

        $adSet = AdSet::create([
            'campaign_id' => $campaign->id,
            'external_id' => 'adset-999',
            'name' => 'Grupo de anúncios',
            'status' => 'ACTIVE',
            'daily_budget' => 75,
            'budget' => 75,
            'strategy' => 'MAXIMIZE_CONVERSIONS',
            'audience' => 'Usuários de interesse em performance',
            'placements' => ['Search', 'Display'],
            'synced_at' => now(),
        ]);

        $this->assertSame(75.0, $adSet->budget);
        $this->assertSame('MAXIMIZE_CONVERSIONS', $adSet->strategy);
        $this->assertSame('Usuários de interesse em performance', $adSet->audience);
        $this->assertSame(['Search', 'Display'], $adSet->placements);
        $this->assertDatabaseHas('ad_sets', [
            'external_id' => 'adset-999',
            'strategy' => 'MAXIMIZE_CONVERSIONS',
            'audience' => 'Usuários de interesse em performance',
        ]);
    }

    public function test_it_updates_meta_campaign_status_through_the_official_api(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://graph.facebook.com/v21.0/campaign-1' => Http::response([]),
        ]);

        $integration = Integration::create([
            'workspace_id' => Workspace::create([
                'name' => 'Client workspace',
                'client_name' => 'Client',
            ])->id,
            'platform' => 'meta',
            'name' => 'Meta Ads',
            'account_id' => '123',
            'ad_account_name' => 'Account',
            'status' => 'CONNECTED',
            'access_token' => 'test-token',
        ]);

        $updated = app(MetaAdsService::class)
            ->updateCampaignStatus($integration, 'campaign-1', 'PAUSED');

        $this->assertTrue($updated);
        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && $request->url() === 'https://graph.facebook.com/v21.0/campaign-1'
            && $request['status'] === 'PAUSED'
        );
    }
}
