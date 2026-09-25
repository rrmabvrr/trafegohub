<?php

namespace App\Services\Webhooks;

use App\Models\Lead;
use App\Models\WebhookEvent;
use Illuminate\Support\Str;

class WebhookEventProcessor
{
    public function process(WebhookEvent $event): void
    {
        $payload = $event->payload ?? [];
        $eventType = $event->event_type ?? 'unknown';

        if ($event->status === 'processed') {
            return;
        }

        match ($event->platform) {
            'meta' => $this->processMetaPayload($eventType, $payload),
            'google' => $this->processGooglePayload($eventType, $payload),
            'tiktok' => $this->processTiktokPayload($eventType, $payload),
            default => null,
        };

        $event->update([
            'status' => 'processed',
            'processed_at' => now(),
            'error_message' => null,
        ]);
    }

    private function processMetaPayload(string $eventType, array $payload): void
    {
        if (str_contains($eventType, 'lead') || isset($payload['entry'])) {
            $this->storeLeadFromPayload('meta', $payload);
        }
    }

    private function processGooglePayload(string $eventType, array $payload): void
    {
        if (str_contains($eventType, 'lead') || isset($payload['lead'])) {
            $this->storeLeadFromPayload('google', $payload);
        }
    }

    private function processTiktokPayload(string $eventType, array $payload): void
    {
        if (str_contains($eventType, 'lead') || isset($payload['data'])) {
            $this->storeLeadFromPayload('tiktok', $payload);
        }
    }

    private function storeLeadFromPayload(string $platform, array $payload): void
    {
        $leadData = $this->extractLeadData($platform, $payload);

        if ($leadData === null) {
            return;
        }

        Lead::updateOrCreate(
            [
                'platform' => $platform,
                'external_lead_id' => $leadData['external_lead_id'] ?? $leadData['email'] ?? Str::uuid()->toString(),
            ],
            [
                'name' => $leadData['name'] ?? 'Lead importado',
                'email' => $leadData['email'] ?? null,
                'phone' => $leadData['phone'] ?? null,
                'source' => 'webhook',
                'origin' => $leadData['origin'] ?? 'webhook',
                'campaign_name' => $leadData['campaign_name'] ?? null,
                'ad_name' => $leadData['ad_name'] ?? null,
                'platform' => $platform,
                'status' => 'novo',
                'lead_date' => now(),
                'observations' => 'Recebido via webhook do provedor '.$platform,
                'metadata' => $payload,
            ],
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractLeadData(string $platform, array $payload): ?array
    {
        return match ($platform) {
            'meta' => [
                'name' => data_get($payload, 'entry.0.changes.0.value.leadgen_id') ?? 'Lead Meta',
                'email' => data_get($payload, 'entry.0.changes.0.value.field_data.0.values.0.value') ?? null,
                'phone' => data_get($payload, 'entry.0.changes.0.value.field_data.1.values.0.value') ?? null,
                'origin' => 'meta lead form',
                'campaign_name' => data_get($payload, 'entry.0.changes.0.value.form_id') ?? null,
                'ad_name' => data_get($payload, 'entry.0.changes.0.value.page_id') ?? null,
                'external_lead_id' => data_get($payload, 'entry.0.changes.0.value.leadgen_id') ?? null,
            ],
            'google' => [
                'name' => data_get($payload, 'lead.name') ?? 'Lead Google',
                'email' => data_get($payload, 'lead.email') ?? null,
                'phone' => data_get($payload, 'lead.phone') ?? null,
                'origin' => 'google lead form',
                'campaign_name' => data_get($payload, 'campaign') ?? null,
                'ad_name' => data_get($payload, 'ad_group') ?? null,
                'external_lead_id' => data_get($payload, 'lead.id') ?? null,
            ],
            'tiktok' => [
                'name' => data_get($payload, 'data.name') ?? 'Lead TikTok',
                'email' => data_get($payload, 'data.email') ?? null,
                'phone' => data_get($payload, 'data.phone') ?? null,
                'origin' => 'tiktok lead form',
                'campaign_name' => data_get($payload, 'data.campaign_name') ?? null,
                'ad_name' => data_get($payload, 'data.ad_name') ?? null,
                'external_lead_id' => data_get($payload, 'data.lead_id') ?? null,
            ],
            default => null,
        };
    }
}
