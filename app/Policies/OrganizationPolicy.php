<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function view(User $user, Organization $organization): bool
    {
        return $user->isOrganizationMember((int) $organization->id);
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->isOrganizationMember((int) $organization->id)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE]);
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $this->update($user, $organization)
            && $user->hasRole(UserRole::ADMIN);
    }
}
