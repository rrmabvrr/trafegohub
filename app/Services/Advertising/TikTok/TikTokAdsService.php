<?php

namespace App\Services\Advertising\TikTok;

use App\Models\Integration;
use App\Services\AbstractAdvertisingPlatformService;

class TikTokAdsService extends AbstractAdvertisingPlatformService
{
    public function platform(): string
    {
        return 'tiktok';
    }

    public function connect(Integration $integration, array $credentials = []): Integration
    {
        return $integration;
    }

    public function disconnect(Integration $integration): void
    {
        // placeholder for OAuth cleanup
    }

    public function getAccounts(Integration $integration): array
    {
        return [];
    }

    public function getCampaigns(Integration $integration, array $filters = []): array
    {
        return [];
    }

    public function getCampaign(Integration $integration, string $externalCampaignId): ?array
    {
        return null;
    }

    public function createCampaign(Integration $integration, array $payload): array
    {
        return [];
    }

    public function updateCampaign(Integration $integration, string $externalCampaignId, array $payload): array
    {
        return [];
    }

    public function pauseCampaign(Integration $integration, string $externalCampaignId): bool
    {
        return false;
    }

    public function activateCampaign(Integration $integration, string $externalCampaignId): bool
    {
        return false;
    }

    public function getAdSets(Integration $integration, string $externalCampaignId): array
    {
        return [];
    }

    public function getAds(Integration $integration, string $externalAdSetId): array
    {
        return [];
    }

    public function getInsights(Integration $integration, string $externalResourceId, array $filters = []): array
    {
        return [];
    }

    public function getConversions(Integration $integration, string $externalResourceId, array $filters = []): array
    {
        return [];
    }
}
