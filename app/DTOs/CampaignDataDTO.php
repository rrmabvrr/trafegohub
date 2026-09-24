<?php

namespace App\DTOs;

readonly class CampaignDataDTO
{
    public function __construct(
        public string $name,
        public string $platform,
        public string $objective,
        public float $dailyBudget,
        public ?string $targetAudience = null,
        public string $status = 'ACTIVE',
        public ?int $integrationId = null,
        public ?int $clientId = null,
        public string $currency = 'BRL',
        public ?string $externalId = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?string $syncedAt = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            platform: $data['platform'],
            objective: $data['objective'] ?? 'SALES',
            dailyBudget: (float) ($data['daily_budget'] ?? 0),
            targetAudience: $data['target_audience'] ?? null,
            status: $data['status'] ?? 'ACTIVE',
            integrationId: isset($data['integration_id']) ? (int) $data['integration_id'] : null,
            clientId: isset($data['client_id']) ? (int) $data['client_id'] : null,
            currency: $data['currency'] ?? 'BRL',
            externalId: $data['external_id'] ?? null,
            startDate: $data['start_date'] ?? null,
            endDate: $data['end_date'] ?? null,
            syncedAt: $data['synced_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'platform' => $this->platform,
            'objective' => $this->objective,
            'daily_budget' => $this->dailyBudget,
            'target_audience' => $this->targetAudience,
            'status' => $this->status,
            'integration_id' => $this->integrationId,
            'client_id' => $this->clientId,
            'currency' => $this->currency,
            'external_id' => $this->externalId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'synced_at' => $this->syncedAt,
        ], fn ($val) => $val !== null);
    }
}

