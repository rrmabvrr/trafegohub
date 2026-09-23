<?php

namespace App\Providers;

use App\Repositories\Contracts\CampaignRepositoryInterface;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Eloquent\CampaignRepository;
use App\Repositories\Eloquent\LeadRepository;
use App\Services\AdvertisingPlatformManager;
use App\Services\GoogleAdsService;
use App\Services\LinkedInAdsService;
use App\Services\MetaAdsService;
use App\Services\MicrosoftAdsService;
use App\Services\TikTokAdsService;
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
            ]);
        });
    }

    public function boot(): void
    {
        //
    }
}
