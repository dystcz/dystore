<?php

use Dystcz\LunarRewards\Domain\Rewards\Calculators\RewardValueCalculator;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Currency;

uses(TestCase::class, RefreshDatabase::class);

it('can calculate value from points', function () {
    /** @var TestCase $this */
    $reward = new Reward(1000);

    $currency = Currency::factory()->create([
        'decimal_places' => 2,
        'exchange_rate' => 1.0,
    ]);

    $calculator = RewardValueCalculator::for($reward, $currency);

    $this->assertEquals(1000, $calculator->calculate()->value);

})->group('rewards', 'value-calculator');
