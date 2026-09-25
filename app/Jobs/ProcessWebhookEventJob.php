<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Models\WebhookFailure;
use App\Services\Webhooks\WebhookEventProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhookEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $webhookEventId) {}

    public function handle(WebhookEventProcessor $processor): void
    {
        $event = WebhookEvent::find($this->webhookEventId);

        if (! $event) {
            return;
        }

        try {
            $processor->process($event);
        } catch (\Throwable $exception) {
            $event->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'retry_count' => $event->retry_count + 1,
            ]);

            WebhookFailure::create([
                'webhook_event_id' => $event->id,
                'platform' => $event->platform,
                'event_type' => $event->event_type,
                'request_id' => $event->request_id,
                'message' => $exception->getMessage(),
                'payload' => $event->payload,
                'status' => 'failed',
            ]);

            throw $exception;
        }
    }
}
