<?php

namespace App\Services;

class PinterestAdsService extends AbstractAdvertisingPlatformService
{
    public function platform(): string
    {
        return 'pinterest';
    }
}
