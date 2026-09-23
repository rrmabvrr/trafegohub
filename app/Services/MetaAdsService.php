<?php

namespace App\Services;

use App\Models\Integration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaAdsService extends AbstractAdvertisingPlatformService
{
    protected string $baseUrl = 'https://graph.facebook.com/v21.0';

    public function platform(): string
    {
        return 'meta';
    }

    public function getCampaigns(Integration $integration, array $filters = []): array
    {
        return $this->fetchCampaignHierarchy($integration);
    }

    public function pauseCampaign(Integration $integration, string $externalCampaignId): bool
    {
        return $this->updateCampaignStatus($integration, $externalCampaignId, 'PAUSED');
    }

    public function activateCampaign(Integration $integration, string $externalCampaignId): bool
    {
        return $this->updateCampaignStatus($integration, $externalCampaignId, 'ACTIVE');
    }

    public function fetchCampaignHierarchy(Integration $integration): array
    {
        try {
            $response = Http::withToken($this->accessToken($integration))
                ->acceptJson()
                ->timeout(30)
                ->retry(3, 250)
                ->get("{$this->baseUrl}/act_{$this->accountId($integration)}/campaigns", [
                    'fields' => implode(',', [
                        'id',
                        'name',
                        'status',
                        'objective',
                        'daily_budget',
                        'start_time',
                        'stop_time',
                        'insights{spend,impressions,clicks,ctr,cpc,conversions,purchase_roas}',
                        'adsets{id,name,status,daily_budget,start_time,end_time,insights{spend,impressions,clicks,ctr,cpc,conversions,purchase_roas},ads{id,name,status,creative{id,name,title,body,image_url},insights{spend,impressions,clicks,ctr,cpc,conversions,purchase_roas}}}',
                    ]),
                ]);

            return $response->throw()->json('data', []);
        } catch (\Throwable $exception) {
            Log::error('Meta Ads API request failed.', [
                'integration_id' => $integration->id,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function updateCampaignStatus(Integration $integration, string $externalCampaignId, string $status): bool
    {
        try {
            return Http::withToken($this->accessToken($integration))
                ->acceptJson()
                ->timeout(30)
                ->retry(3, 250)
                ->post("{$this->baseUrl}/{$externalCampaignId}", [
                    'status' => $status,
                ])
                ->successful();
        } catch (\Throwable $exception) {
            Log::error('Meta Ads campaign update failed.', [
                'integration_id' => $integration->id,
                'external_campaign_id' => $externalCampaignId,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function accessToken(Integration $integration): string
    {
        return (string) $integration->access_token;
    }

    private function accountId(Integration $integration): string
    {
        return preg_replace('/^act_/', '', trim($integration->account_id)) ?? trim($integration->account_id);
    }
}
