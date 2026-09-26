<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogger
{
    public function log(
        string $action,
        string $resourceType,
        int|string|null $resourceId,
        array $data = [],
        ?User $user = null,
        ?int $organizationId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $user?->id,
            'organization_id' => $organizationId,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'details' => $data,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
