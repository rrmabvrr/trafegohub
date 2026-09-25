<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWebhookEventJob;
use App\Models\WebhookEvent;
use App\Models\WebhookFailure;
use App\Services\Webhooks\WebhookSignatureValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function __construct(
        protected WebhookSignatureValidator $signatureValidator,
    ) {}

    public function handle(string $platform, Request $request): JsonResponse
    {
        $payload = $request->all();
        [$isValid, $errorMessage] = $this->signatureValidator->validate($platform, $request);

        if (! $isValid) {
            WebhookFailure::create([
                'platform' => $platform,
                'event_type' => 'signature_validation_failed',
                'request_id' => $request->header('x-request-id') ?? Str::uuid()->toString(),
                'message' => $errorMessage,
                'payload' => $payload,
                'status' => 'failed',
            ]);

            return response()->json(['message' => $errorMessage], 401);
        }

        $providerEventId = $this->resolveProviderEventId($platform, $payload, $request);
        $eventHash = md5(json_encode(['platform' => $platform, 'payload' => $payload], JSON_THROW_ON_ERROR));

        $event = WebhookEvent::firstOrCreate(
            [
                'platform' => $platform,
                'provider_event_id' => $providerEventId ?? $eventHash,
            ],
            [
                'event_type' => $this->resolveEventType($payload),
                'request_id' => $request->header('x-request-id') ?? Str::uuid()->toString(),
                'signature_valid' => true,
                'status' => 'received',
                'headers' => $request->headers->all(),
                'payload' => $payload,
                'retry_count' => 0,
            ],
        );

        if ($event->wasRecentlyCreated === false && $event->status === 'processed') {
            return response()->json(['message' => 'Webhook already processed.'], 202);
        }

        $event->update([
            'event_type' => $this->resolveEventType($payload),
            'request_id' => $request->header('x-request-id') ?? $event->request_id,
            'signature_valid' => true,
            'status' => 'queued',
            'headers' => $request->headers->all(),
            'payload' => $payload,
        ]);

        ProcessWebhookEventJob::dispatch($event->id)->onQueue('webhooks');

        return response()->json(['message' => 'Webhook accepted for processing.', 'id' => $event->id], 202);
    }

    private function resolveProviderEventId(string $platform, array $payload, Request $request): ?string
    {
        $candidate = data_get($payload, 'event.id')
            ?? data_get($payload, 'lead.id')
            ?? data_get($payload, 'data.id')
            ?? data_get($payload, 'entry.0.id')
            ?? data_get($payload, 'entry.0.changes.0.value.leadgen_id')
            ?? $request->header('x-webhook-id');

        return is_scalar($candidate) ? (string) $candidate : null;
    }

    private function resolveEventType(array $payload): string
    {
        return data_get($payload, 'event_type')
            ?? data_get($payload, 'event.type')
            ?? data_get($payload, 'type')
            ?? 'lead_received';
    }
}
