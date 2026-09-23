<?php

namespace App\Services;

use App\Models\Integration;
use App\Services\Contracts\AdvertisingPlatformInterface;
use LogicException;

abstract class AbstractAdvertisingPlatformService implements AdvertisingPlatformInterface
{
    public function connect(Integration $integration, array $credentials = []): Integration
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function disconnect(Integration $integration): void
    {
        $this->unsupported(__FUNCTION__);
    }

    public function getAccounts(Integration $integration): array
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function getCampaigns(Integration $integration, array $filters = []): array
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function getCampaign(Integration $integration, string $externalCampaignId): ?array
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function createCampaign(Integration $integration, array $payload): array
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function updateCampaign(Integration $integration, string $externalCampaignId, array $payload): array
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function pauseCampaign(Integration $integration, string $externalCampaignId): bool
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function activateCampaign(Integration $integration, string $externalCampaignId): bool
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function getAdSets(Integration $integration, string $externalCampaignId): array
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function getAds(Integration $integration, string $externalAdSetId): array
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function getInsights(Integration $integration, string $externalResourceId, array $filters = []): array
    {
        return $this->unsupported(__FUNCTION__);
    }

    public function getConversions(Integration $integration, string $externalResourceId, array $filters = []): array
    {
        return $this->unsupported(__FUNCTION__);
    }

    protected function unsupported(string $operation): never
    {
        throw new LogicException(sprintf(
            'The %s operation is not configured for the %s platform.',
            $operation,
            $this->platform(),
        ));
    }
}
