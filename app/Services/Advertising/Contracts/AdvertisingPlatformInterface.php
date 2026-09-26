<?php

namespace App\Services\Advertising\Contracts;

use App\Models\Integration;

interface AdvertisingPlatformInterface
{
    public function platform(): string;

    public function connect(Integration $integration, array $credentials = []): Integration;

    public function disconnect(Integration $integration): void;

    /** @return list<array<string, mixed>> */
    public function getAccounts(Integration $integration): array;

    /** @return list<array<string, mixed>> */
    public function getCampaigns(Integration $integration, array $filters = []): array;

    /** @return array<string, mixed>|null */
    public function getCampaign(Integration $integration, string $externalCampaignId): ?array;

    /** @return array<string, mixed> */
    public function createCampaign(Integration $integration, array $payload): array;

    /** @return array<string, mixed> */
    public function updateCampaign(Integration $integration, string $externalCampaignId, array $payload): array;

    public function pauseCampaign(Integration $integration, string $externalCampaignId): bool;

    public function activateCampaign(Integration $integration, string $externalCampaignId): bool;

    /** @return list<array<string, mixed>> */
    public function getAdSets(Integration $integration, string $externalCampaignId): array;

    /** @return list<array<string, mixed>> */
    public function getAds(Integration $integration, string $externalAdSetId): array;

    /** @return list<array<string, mixed>> */
    public function getInsights(Integration $integration, string $externalResourceId, array $filters = []): array;

    /** @return list<array<string, mixed>> */
    public function getConversions(Integration $integration, string $externalResourceId, array $filters = []): array;
}
