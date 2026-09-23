<?php

namespace App\Services;

use App\Models\Integration;
use App\Services\Contracts\AdvertisingPlatformInterface;
use InvalidArgumentException;

class AdvertisingPlatformManager
{
    /**
     * @param  iterable<AdvertisingPlatformInterface>  $platforms
     */
    public function __construct(private readonly iterable $platforms) {}

    public function for(Integration $integration): AdvertisingPlatformInterface
    {
        foreach ($this->platforms as $platform) {
            if ($platform->platform() === $integration->platform) {
                return $platform;
            }
        }

        throw new InvalidArgumentException(sprintf(
            'No advertising platform adapter is registered for [%s].',
            $integration->platform,
        ));
    }
}
