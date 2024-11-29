<?php

use Dystcz\LunarRewards\Domain\Rewards\Actions\CreateCouponFromBalance;
use Dystcz\LunarRewards\Domain\Rewards\Actions\DepositPoints;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;
use Dystcz\LunarRewards\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Models\Currency;

uses(TestCase::class, RefreshDatabase::class);

it('can create a coupon from reward points balance', function () {

    /** @var TestCase $this */
    $currency = Currency::factory()->create([
        'decimal_places' => 2,
        'exchange_rate' => 1.0,
    ]);

    $user = $this->createUser();

    $balance = PointBalanceManager::of($user);

    (new DepositPoints)->handle(to: $user, points: new Reward(1000));

    $coupon = App::make(CreateCouponFromBalance::class)->handle($user, $currency);

    $this->assertEquals($coupon->data, [
        'fixed_value' => true,
        'fixed_values' => [
            $currency->code => 10,
        ],
    ]);

})->group('rewards', 'coupons');
