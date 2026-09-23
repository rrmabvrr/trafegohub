<?php

namespace App\Services;

class MicrosoftAdsService extends AbstractAdvertisingPlatformService
{
    public function platform(): string
    {
        return 'microsoft';
    }
}
