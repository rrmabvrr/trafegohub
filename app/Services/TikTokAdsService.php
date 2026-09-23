<?php

namespace App\Services;

class TikTokAdsService extends AbstractAdvertisingPlatformService
{
    public function platform(): string
    {
        return 'tiktok';
    }
}
