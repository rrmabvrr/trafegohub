<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\Report;
use App\Policies\CampaignPolicy;
use App\Policies\ClientPolicy;
use App\Policies\ReportPolicy;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Eloquent\CampaignRepository;
use App\Repositories\Eloquent\LeadRepository;
use App\Services\AdvertisingPlatformManager;
use App\Services\GoogleAdsService;
use App\Services\LinkedInAdsService;
use App\Services\MetaAdsService;
use App\Services\MicrosoftAdsService;
use App\Services\PinterestAdsService;
use App\Services\TikTokAdsService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CampaignRepositoryInterface::class, CampaignRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->singleton(AdvertisingPlatformManager::class, function ($app): AdvertisingPlatformManager {
            return new AdvertisingPlatformManager([
                $app->make(MetaAdsService::class),
                $app->make(GoogleAdsService::class),
                $app->make(TikTokAdsService::class),
                $app->make(LinkedInAdsService::class),
                $app->make(MicrosoftAdsService::class),
                $app->make(PinterestAdsService::class),
            ]);
        });
    }

    public function boot(): void
    {
        Gate::before(function ($user): ?bool {
            return $user->hasRole(UserRole::ADMIN) ? true : null;
        });

        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Campaign::class, CampaignPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);
    }
}
