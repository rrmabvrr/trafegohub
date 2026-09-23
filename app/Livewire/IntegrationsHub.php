<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Integration;
use App\Models\Organization;
use Illuminate\Validation\Rule;
use Livewire\Component;

class IntegrationsHub extends Component
{
    public string $search = '';

    public string $platformFilter = 'all';

    public string $statusFilter = 'all';

    public bool $showModal = false;

    public ?int $editingIntegrationId = null;

    public ?int $clientId = null;

    public string $platform = 'meta';

    public string $name = '';

    public string $accountId = '';

    public string $status = 'DISCONNECTED';

    public string $currency = 'BRL';

    public string $timezone = 'America/Sao_Paulo';

    public function createIntegration(): void
    {
        $organizationId = $this->organizationId();
        $this->authorize('create', [Integration::class, $organizationId]);
        $this->validate();

        $client = Client::where('organization_id', $organizationId)->findOrFail($this->clientId);
        $workspace = $client->workspaces()->firstOrCreate(
            ['name' => $client->name.' Workspace'],
            ['client_name' => $client->name, 'organization_id' => $organizationId],
        );

        Integration::create($this->integrationData() + [
            'organization_id' => $organizationId,
            'workspace_id' => $workspace->id,
            'external_account_id' => $this->accountId,
            'connected_user_id' => auth()->id(),
            'connected_at' => now(),
        ]);

        $this->resetForm();
        $this->dispatch('notify', ['message' => 'Conta de publicidade criada com sucesso.']);
    }

    public function editIntegration(int $integrationId): void
    {
        $integration = $this->integrationQuery()->findOrFail($integrationId);
        $this->authorize('update', $integration);

        $this->editingIntegrationId = $integration->id;
        $this->clientId = $integration->client_id ?: $integration->workspace?->client_id;
        $this->platform = $integration->platform;
        $this->name = $integration->name;
        $this->accountId = $integration->account_id;
        $this->status = $integration->status;
        $this->currency = $integration->currency ?: 'BRL';
        $this->timezone = $integration->timezone ?: 'America/Sao_Paulo';
        $this->showModal = true;
    }

    public function updateIntegration(): void
    {
        $integration = $this->integrationQuery()->findOrFail($this->editingIntegrationId);
        $this->authorize('update', $integration);
        $this->validate();

        $integration->update($this->integrationData());

        $this->resetForm();
        $this->dispatch('notify', ['message' => 'Conta de publicidade atualizada com sucesso.']);
    }

    public function deleteIntegration(int $integrationId): void
    {
        $integration = $this->integrationQuery()->findOrFail($integrationId);
        $this->authorize('delete', $integration);
        $integration->delete();

        $this->dispatch('notify', ['message' => 'Conta de publicidade excluída com sucesso.']);
    }

    public function toggleConnection(int $integrationId): void
    {
        $integration = $this->integrationQuery()->findOrFail($integrationId);
        $this->authorize('update', $integration);

        $integration->update([
            'status' => $integration->status === 'CONNECTED' ? 'DISCONNECTED' : 'CONNECTED',
        ]);
    }

    /**
     * Receives credentials only for the current request; they are never component state.
     * OAuth callbacks can use this action after exchanging the authorization code.
     *
     * @param  list<string>  $scopes
     */
    public function saveCredentials(
        int $integrationId,
        string $accessToken,
        ?string $refreshToken = null,
        ?int $expiresIn = null,
        array $scopes = [],
        ?string $externalAccountId = null,
    ): void {
        $integration = $this->integrationQuery()->findOrFail($integrationId);
        $this->authorize('update', $integration);

        $integration->update([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken ?: $integration->refresh_token,
            'expires_at' => $expiresIn ? now()->addSeconds($expiresIn) : $integration->expires_at,
            'scopes' => $scopes,
            'external_account_id' => $externalAccountId ?: $integration->external_account_id,
            'connected_user_id' => auth()->id(),
            'connected_at' => now(),
            'status' => 'CONNECTED',
            'last_error' => null,
        ]);
    }

    public function closeModal(): void
    {
        $this->resetForm();
    }

    protected function rules(): array
    {
        return [
            'clientId' => ['required', Rule::exists('clients', 'id')->where('organization_id', $this->organizationId())],
            'platform' => ['required', Rule::in(['meta', 'google', 'tiktok', 'linkedin', 'microsoft', 'pinterest', 'kwai'])],
            'name' => ['required', 'string', 'max:255'],
            'accountId' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['CONNECTED', 'DISCONNECTED', 'ERROR', 'SYNCING'])],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'timezone'],
        ];
    }

    public function render()
    {
        $query = $this->integrationQuery()->with(['client', 'workspace.client', 'connectedUser']);

        if ($this->search !== '') {
            $query->where(function ($builder): void {
                $builder->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('ad_account_name', 'like', '%'.$this->search.'%')
                    ->orWhere('account_id', 'like', '%'.$this->search.'%');
            });
        }
        if ($this->platformFilter !== 'all') {
            $query->where('platform', $this->platformFilter);
        }
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.integrations-hub', [
            'integrations' => $query->latest()->get(),
            'clients' => Client::where('organization_id', $this->organizationId())->orderBy('name')->get(),
        ])->layout('layouts.app');
    }

    private function organizationId(): int
    {
        $user = auth()->user();
        if ($user?->active_organization_id) {
            return (int) $user->active_organization_id;
        }
        if ($user?->organizations()->exists()) {
            return (int) $user->organizations()->first()->id;
        }

        return (int) (Organization::query()->value('id') ?? 0);
    }

    private function integrationQuery()
    {
        return Integration::where('organization_id', $this->organizationId());
    }

    private function integrationData(): array
    {
        return [
            'client_id' => $this->clientId,
            'platform' => $this->platform,
            'name' => $this->name,
            'account_id' => $this->accountId,
            'external_account_id' => $this->accountId,
            'ad_account_name' => $this->name,
            'status' => $this->status,
            'currency' => strtoupper($this->currency),
            'timezone' => $this->timezone,
        ];
    }

    private function resetForm(): void
    {
        $this->reset([
            'showModal',
            'editingIntegrationId',
            'clientId',
            'name',
            'accountId',
        ]);
        $this->platform = 'meta';
        $this->status = 'DISCONNECTED';
        $this->currency = 'BRL';
        $this->timezone = 'America/Sao_Paulo';
        $this->resetValidation();
    }
}
