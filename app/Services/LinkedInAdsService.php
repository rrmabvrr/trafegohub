<?php

namespace App\Services;

class LinkedInAdsService extends AbstractAdvertisingPlatformService
{
    public function platform(): string
    {
        return 'linkedin';
    }
}
