<?php

namespace App\Services\Webhooks;

use Illuminate\Http\Request;

class WebhookSignatureValidator
{
    /**
     * @return array{0: bool, 1: ?string}
     */
    public function validate(string $platform, Request $request): array
    {
        $secret = config("services.webhooks.{$platform}.secret");

        if (blank($secret)) {
            return [false, 'Webhook secret is not configured for this platform.'];
        }

        $signature = $this->extractSignatureHeader($platform, $request);

        if (blank($signature)) {
            return [false, 'Missing webhook signature header.'];
        }

        $payload = $request->getContent();
        $expected = $this->buildExpectedSignature($payload, $secret);

        if (! hash_equals($expected, $signature)) {
            return [false, 'Invalid webhook signature.'];
        }

        return [true, null];
    }

    private function extractSignatureHeader(string $platform, Request $request): ?string
    {
        $headers = [
            'meta' => ['x-hub-signature-256', 'x-hub-signature'],
            'google' => ['x-goog-signature', 'x-signature'],
            'tiktok' => ['x-tiktok-signature', 'x-signature'],
        ];

        foreach ($headers[$platform] ?? ['x-signature', 'x-webhook-signature'] as $header) {
            $value = $request->header($header);

            if (filled($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    private function buildExpectedSignature(string $payload, string $secret): string
    {
        $hash = hash_hmac('sha256', $payload, $secret);

        return 'sha256='.$hash;
    }
}
