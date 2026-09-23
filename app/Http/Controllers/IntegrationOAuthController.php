<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IntegrationOAuthController extends Controller
{
    public function redirect(Request $request, Integration $integration): RedirectResponse
    {
        $this->authorize('update', $integration);

        $oauth = config('services.oauth.'.$integration->platform);
        abort_unless($oauth['authorize_url'] && $oauth['client_id'], Response::HTTP_NOT_IMPLEMENTED);

        $state = Str::random(64);
        $request->session()->put('integration_oauth', [
            'state' => $state,
            'integration_id' => $integration->id,
        ]);

        return redirect()->away($oauth['authorize_url'].'?'.http_build_query([
            'client_id' => $oauth['client_id'],
            'redirect_uri' => route('integrations.oauth.callback'),
            'response_type' => 'code',
            'scope' => implode(' ', $oauth['scopes'] ?? []),
            'state' => $state,
        ]));
    }

    public function callback(Request $request): RedirectResponse
    {
        $oauthState = $request->session()->pull('integration_oauth');
        abort_unless(is_array($oauthState) && hash_equals((string) ($oauthState['state'] ?? ''), (string) $request->string('state')), Response::HTTP_FORBIDDEN);

        $integration = Integration::findOrFail((int) $oauthState['integration_id']);
        $this->authorize('update', $integration);

        $oauth = config('services.oauth.'.$integration->platform);
        $tokenResponse = Http::asForm()->post($oauth['token_url'], [
            'grant_type' => 'authorization_code',
            'client_id' => $oauth['client_id'],
            'client_secret' => $oauth['client_secret'],
            'redirect_uri' => route('integrations.oauth.callback'),
            'code' => $request->string('code')->toString(),
        ])->throw()->json();

        $integration->update([
            'access_token' => $tokenResponse['access_token'],
            'refresh_token' => $tokenResponse['refresh_token'] ?? $integration->refresh_token,
            'expires_at' => isset($tokenResponse['expires_in']) ? now()->addSeconds((int) $tokenResponse['expires_in']) : null,
            'scopes' => $this->scopes($tokenResponse, $oauth),
            'connected_user_id' => $request->user()->id,
            'connected_at' => now(),
            'status' => 'CONNECTED',
            'last_error' => null,
        ]);

        return redirect()->route('integrations')->with('status', 'Conta conectada via OAuth.');
    }

    /** @return list<string> */
    private function scopes(array $tokenResponse, array $oauth): array
    {
        $scope = $tokenResponse['scope'] ?? $oauth['scopes'] ?? [];

        return is_array($scope) ? array_values($scope) : preg_split('/\s+/', (string) $scope, -1, PREG_SPLIT_NO_EMPTY);
    }
}
