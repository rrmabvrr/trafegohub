<?php

namespace Tests\Unit;

use App\Models\Integration;
use App\Services\AdvertisingPlatformManager;
use LogicException;
use Tests\TestCase;

class AdvertisingPlatformManagerTest extends TestCase
{
    public function test_it_resolves_each_registered_platform_independently(): void
    {
        $manager = app(AdvertisingPlatformManager::class);

        foreach (['meta', 'google', 'tiktok', 'linkedin', 'microsoft'] as $platform) {
            $integration = new Integration(['platform' => $platform]);

            $this->assertSame($platform, $manager->for($integration)->platform());
        }
    }

    public function test_unconfigured_platform_operations_are_explicitly_rejected(): void
    {
        $manager = app(AdvertisingPlatformManager::class);
        $integration = new Integration(['platform' => 'google']);

        $this->expectException(LogicException::class);
        $manager->for($integration)->getCampaigns($integration);
    }
}
