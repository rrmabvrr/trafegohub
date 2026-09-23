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
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            platform: $data['platform'],
            objective: $data['objective'] ?? 'SALES',
            dailyBudget: (float) $data['daily_budget'],
            targetAudience: $data['target_audience'] ?? null,
            status: $data['status'] ?? 'ACTIVE',
            integrationId: isset($data['integration_id']) ? (int) $data['integration_id'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'platform' => $this->platform,
            'objective' => $this->objective,
            'daily_budget' => $this->dailyBudget,
            'target_audience' => $this->targetAudience,
            'status' => $this->status,
            'integration_id' => $this->integrationId,
        ];
    }
}
