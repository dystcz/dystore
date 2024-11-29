<?php

use Dystcz\LunarRewards\Domain\Rewards\Actions\DepositPoints;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;
use Dystcz\LunarRewards\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can deposit correct point amount for a model', function () {

    /** @var TestCase $this */
    $user = $this->createUser();

    $balance = PointBalanceManager::of($user);

    (new DepositPoints)->handle(to: $user, points: new Reward(100));

    $this->assertEquals(100, $balance->getValue());
})->group('rewards', 'balance', 'deposit');
