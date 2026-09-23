<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Integration;
use App\Models\User;

class IntegrationPolicy
{
    public function viewAny(User $user, int $organizationId): bool
    {
        return $user->isOrganizationMember($organizationId);
    }

    public function create(User $user, int $organizationId): bool
    {
        return $user->isOrganizationMember($organizationId)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE]);
    }

    public function update(User $user, Integration $integration): bool
    {
        return $user->isOrganizationMember((int) $integration->organization_id)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE]);
    }

    public function delete(User $user, Integration $integration): bool
    {
        return $this->update($user, $integration);
    }
}
