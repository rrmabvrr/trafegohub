<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Organization;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ClientsManager extends Component
{
    public string $search = '';

    public string $statusFilter = 'all';

    public bool $showModal = false;

    public ?int $editingClientId = null;

    public string $name = '';

    public string $legalName = '';

    public string $document = '';

    public string $email = '';

    public string $phone = '';

    public string $whatsapp = '';

    public string $address = '';

    public string $status = 'active';

    public string $notes = '';

    public function createClient(): void
    {
        $organizationId = $this->organizationId();
        $this->authorize('create', [Client::class, $organizationId]);

        $this->validate();

        Client::create($this->clientData() + ['organization_id' => $organizationId]);

        $this->resetForm();
        $this->dispatch('notify', ['message' => 'Cliente criado com sucesso.']);
    }

    public function editClient(int $clientId): void
    {
        $client = $this->clientQuery()->findOrFail($clientId);
        $this->authorize('update', $client);

        $this->editingClientId = $client->id;
        $this->name = $client->name;
        $this->legalName = (string) $client->legal_name;
        $this->document = (string) $client->document;
        $this->email = (string) $client->email;
        $this->phone = (string) $client->phone;
        $this->whatsapp = (string) $client->whatsapp;
        $this->address = (string) $client->address;
        $this->status = (string) $client->status;
        $this->notes = (string) $client->notes;
        $this->showModal = true;
    }

    public function updateClient(): void
    {
        $client = $this->clientQuery()->findOrFail($this->editingClientId);
        $this->authorize('update', $client);

        $this->validate();
        $client->update($this->clientData());

        $this->resetForm();
        $this->dispatch('notify', ['message' => 'Cliente atualizado com sucesso.']);
    }

    public function deleteClient(int $clientId): void
    {
        $client = $this->clientQuery()->findOrFail($clientId);
        $this->authorize('delete', $client);
        $client->delete();

        $this->dispatch('notify', ['message' => 'Cliente excluído com sucesso.']);
    }

    public function closeModal(): void
    {
        $this->resetForm();
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'legalName' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function render()
    {
        $query = $this->clientQuery()->withCount('advertisingAccounts');

        if ($this->search !== '') {
            $query->where(function ($builder): void {
                $builder->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('legal_name', 'like', '%'.$this->search.'%')
                    ->orWhere('document', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.clients-manager', [
            'clients' => $query->orderBy('name')->get(),
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

    private function clientQuery()
    {
        return Client::where('organization_id', $this->organizationId());
    }

    private function clientData(): array
    {
        return [
            'name' => $this->name,
            'legal_name' => $this->legalName ?: null,
            'document' => $this->document ?: null,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'whatsapp' => $this->whatsapp ?: null,
            'address' => $this->address ?: null,
            'status' => $this->status,
            'notes' => $this->notes ?: null,
        ];
    }

    private function resetForm(): void
    {
        $this->reset([
            'showModal',
            'editingClientId',
            'name',
            'legalName',
            'document',
            'email',
            'phone',
            'whatsapp',
            'address',
            'notes',
        ]);
        $this->status = 'active';
        $this->resetValidation();
    }
}
