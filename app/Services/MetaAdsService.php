<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaAdsService
{
    protected string $baseUrl = 'https://graph.facebook.com/v19.0';

    public function fetchAccountCampaigns(string $accessToken, string $accountId): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/act_{$accountId}/campaigns", [
                    'fields' => 'id,name,status,objective,daily_budget,insights{spend,impressions,clicks,ctr,cpc,conversions,purchase_roas}',
                ]);

            if ($response->successful()) {
                return $response->json('data', []);
            }

            Log::error("Meta Ads API Error: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("Meta Ads Exception: " . $e->getMessage());
            return [];
        }
    }

    public function updateCampaignStatus(string $accessToken, string $campaignId, string $status): bool
    {
        try {
            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl}/{$campaignId}", [
                    'status' => $status,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Meta Ads Update Exception: " . $e->getMessage());
            return false;
        }
    }
}
