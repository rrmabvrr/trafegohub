<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use App\Repositories\Eloquent\CampaignRepository;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Eloquent\LeadRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CampaignRepositoryInterface::class, CampaignRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
