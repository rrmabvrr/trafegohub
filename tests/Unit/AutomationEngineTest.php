<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\DTOs\CampaignDataDTO;

class AutomationEngineTest extends TestCase
{
    public function test_campaign_dto_instantiation(): void
    {
        $dto = new CampaignDataDTO(
            name: '[Meta] Campanha Teste Unitário',
            platform: 'meta',
            objective: 'SALES',
            dailyBudget: 250.00,
            targetAudience: 'Lookalike 1%'
        );

        $this->assertEquals('[Meta] Campanha Teste Unitário', $dto->name);
        $this->assertEquals('meta', $dto->platform);
        $this->assertEquals(250.00, $dto->dailyBudget);
    }
}
