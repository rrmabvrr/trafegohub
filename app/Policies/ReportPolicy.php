<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user, int $organizationId): bool
    {
        return $user->isOrganizationMember($organizationId);
    }

    public function view(User $user, Report $report): bool
    {
        if (! $user->isOrganizationMember((int) $report->organization_id)) {
            return false;
        }

        if (! $user->hasRole(UserRole::CLIENTE)) {
            return true;
        }

        return $user->clientIdForOrganization((int) $report->organization_id)
            === $report->client_id;
    }

    public function create(User $user, int $organizationId): bool
    {
        return $user->isOrganizationMember($organizationId)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE, UserRole::ANALISTA]);
    }

    public function update(User $user, Report $report): bool
    {
        return $this->view($user, $report)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE, UserRole::ANALISTA]);
    }

    public function delete(User $user, Report $report): bool
    {
        return $this->view($user, $report)
            && $user->hasAnyRole([UserRole::ADMIN, UserRole::GERENTE]);
    }
}
