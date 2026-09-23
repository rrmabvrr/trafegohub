<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewAny(User $user, int $organizationId): bool
    {
        return $user->isOrganizationMember($organizationId);
    }

    public function view(User $user, Campaign $campaign): bool
    {
        return $this->canAccessCampaign($user, $campaign);
    }

    public function create(User $user, int $organizationId): bool
    {
        return $user->isOrganizationMember($organizationId)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE]);
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return $this->canAccessCampaign($user, $campaign)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE, UserRole::ANALISTA]);
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        return $this->canAccessCampaign($user, $campaign)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE]);
    }

    private function canAccessCampaign(User $user, Campaign $campaign): bool
    {
        if (! $user->isOrganizationMember((int) $campaign->organization_id)) {
            return false;
        }

        if (! $user->hasRole(UserRole::CLIENTE)) {
            return true;
        }

        return $user->clientIdForOrganization((int) $campaign->organization_id)
            === $campaign->workspace?->client_id;
    }
}
