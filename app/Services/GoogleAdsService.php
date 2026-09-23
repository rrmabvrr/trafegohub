<?php

namespace App\Services;

class GoogleAdsService extends AbstractAdvertisingPlatformService
{
    public function platform(): string
    {
        return 'google';
    }
}
