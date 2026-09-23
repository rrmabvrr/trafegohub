<?php

namespace App\DTOs;

readonly class LeadDataDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public string $platform = 'meta',
        public ?string $utmSource = null,
        public ?string $utmMedium = null,
        public ?string $utmCampaign = null,
        public float $cpl = 0.0,
        public float $dealValue = 0.0,
        public string $status = 'NEW',
        public ?string $city = null,
        public ?int $campaignId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            platform: $data['platform'] ?? 'meta',
            utmSource: $data['utm_source'] ?? null,
            utmMedium: $data['utm_medium'] ?? null,
            utmCampaign: $data['utm_campaign'] ?? null,
            cpl: (float) ($data['cpl'] ?? 0),
            dealValue: (float) ($data['deal_value'] ?? 0),
            status: $data['status'] ?? 'NEW',
            city: $data['city'] ?? null,
            campaignId: isset($data['campaign_id']) ? (int) $data['campaign_id'] : null,
        );
    }
}
