<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Integration;
use App\Models\Organization;
use App\Models\Report;
use App\Policies\CampaignPolicy;
use App\Policies\ClientPolicy;
use App\Policies\IntegrationPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\ReportPolicy;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Eloquent\CampaignRepository;
use App\Repositories\Eloquent\LeadRepository;
use App\Services\Advertising\Google\GoogleAdsService;
use App\Services\Advertising\LinkedIn\LinkedInAdsService;
use App\Services\Advertising\Meta\MetaAdsService;
use App\Services\Advertising\TikTok\TikTokAdsService;
use App\Services\AdvertisingPlatformManager;
use App\Services\Audit\AuditLogger;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CampaignRepositoryInterface::class, CampaignRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->singleton(AuditLogger::class, fn () => new AuditLogger);
        $this->app->singleton(AdvertisingPlatformManager::class, function ($app): AdvertisingPlatformManager {
            return new AdvertisingPlatformManager([
                $app->make(MetaAdsService::class),
                $app->make(GoogleAdsService::class),
                $app->make(TikTokAdsService::class),
                $app->make(LinkedInAdsService::class),
            ]);
        });
    }

    public function boot(): void
    {
        Gate::before(function ($user): ?bool {
            return $user->hasRole(UserRole::ADMIN) ? true : null;
        });

        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Integration::class, IntegrationPolicy::class);
        Gate::policy(Campaign::class, CampaignPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);
    }
}
