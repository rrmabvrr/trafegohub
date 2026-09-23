<?php

namespace App\Services\Contracts;

use App\Models\Integration;

interface AdvertisingPlatformService
{
    /**
     * @return list<array<string, mixed>>
     */
    public function fetchCampaignHierarchy(Integration $integration): array;

    public function updateCampaignStatus(Integration $integration, string $externalCampaignId, string $status): bool;
}
