<?php

use Dystcz\LunarRewards\Domain\Rewards\Actions\ChargePoints;
use Dystcz\LunarRewards\Domain\Rewards\Actions\DepositPoints;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;
use Dystcz\LunarRewards\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use O21\LaravelWallet\Exception\InsufficientFundsException;

uses(TestCase::class, RefreshDatabase::class);

it('can charge correct point amount for a model', function () {

    /** @var TestCase $this */
    $user = $this->createUser();

    $balance = PointBalanceManager::of($user);

    (new DepositPoints)->handle(to: $user, points: new Reward(1000));
    (new ChargePoints)->handle(from: $user, points: new Reward(200));
    (new ChargePoints)->handle(from: $user, points: new Reward(300));
    (new ChargePoints)->handle(from: $user, points: new Reward(50));

    $this->assertEquals(450, $balance->getValue());
})->group('rewards', 'balance', 'charge');

it('it throws an exception if there are not enough funds to charge', function () {

    /** @var TestCase $this */
    $user = $this->createUser();

    $balance = PointBalanceManager::of($user);

    (new DepositPoints)->handle(to: $user, points: new Reward(1000));

    $this->assertThrows(
        fn () => (new ChargePoints)->handle(from: $user, points: new Reward(1200)),
        InsufficientFundsException::class,
    );

})->group('rewards', 'balance', 'charge');
