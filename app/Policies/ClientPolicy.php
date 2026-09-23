<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user, int $organizationId): bool
    {
        return $user->isOrganizationMember($organizationId);
    }

    public function view(User $user, Client $client): bool
    {
        if (! $user->isOrganizationMember($client->organization_id)) {
            return false;
        }

        return ! $user->hasRole(UserRole::CLIENTE)
            || $user->clientIdForOrganization($client->organization_id) === $client->id;
    }

    public function create(User $user, int $organizationId): bool
    {
        return $user->isOrganizationMember($organizationId)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE]);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->isOrganizationMember($client->organization_id)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE]);
    }

    public function delete(User $user, Client $client): bool
    {
        return $this->update($user, $client);
    }
}
