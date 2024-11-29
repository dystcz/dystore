<?php

use Dystcz\LunarRewards\Domain\Rewards\Calculators\RewardPointsCalculator;
use Dystcz\LunarRewards\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Lunar\Models\Currency;

uses(TestCase::class, RefreshDatabase::class);

it('can calculate points from order', function () {

    /** @var TestCase $this */
    $currency = Currency::factory()->create([
        'decimal_places' => 2,
        'exchange_rate' => 1.0,
    ]);

    // Order subtotal is 10
    $order = $this->createOrder(
        currency: $currency,
    );

    $reward = RewardPointsCalculator::for($order)
        ->setRewardCoefficient(2)
        ->calculate();

    $this->assertEquals(20, $reward->value);

})->group('rewards', 'point-calculator');

it('can calculate points from order while respecting currency exchange rate', function () {

    /** @var TestCase $this */
    $currency = Currency::factory()->create([
        'decimal_places' => 2,
        'exchange_rate' => 1.5,
    ]);

    // Order subtotal is 10
    $order = $this->createOrder(
        currency: $currency,
    );

    $reward = RewardPointsCalculator::for($order)
        ->setRewardCoefficient(10)
        ->calculate();

    $this->assertEquals(150, $reward->value);

})->group('rewards', 'point-calculator');

it('can calculate points from order respecting configured', function () {

    /** @var TestCase $this */
    $currency = Currency::factory()->create([
        'decimal_places' => 2,
        'exchange_rate' => 1,
    ]);

    Config::set('lunar-rewards.reward_point_coefficient', 10);

    // Order subtotal is 10
    $order = $this->createOrder(
        currency: $currency,
    );

    $reward = RewardPointsCalculator::for($order)
        ->calculate();

    $this->assertEquals(100, $reward->value);

})->group('rewards', 'point-calculator');

it('can calculate points from cart correctly', function () {

    /** @var TestCase $this */
    $currency = Currency::factory()->create([
        'decimal_places' => 2,
        'exchange_rate' => 1.0,
    ]);

    // Cart subtotal is 20
    $cart = $this->createCart(
        currency: $currency,
    );

    $reward = RewardPointsCalculator::for($cart)
        ->setRewardCoefficient(3)
        ->calculate();

    $this->assertEquals(60, $reward->value);

})->group('rewards', 'point-calculator');
