<?php

namespace Dystcz\LunarRewards\Tests\Unit\Domain\Rewards\Managers;

use Dystcz\LunarRewards\Domain\Rewards\Actions\ChargePoints;
use Dystcz\LunarRewards\Domain\Rewards\Actions\DepositPoints;
use Dystcz\LunarRewards\Domain\Rewards\Actions\TransferPoints;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;
use Dystcz\LunarRewards\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use O21\LaravelWallet\Numeric;

uses(TestCase::class, RefreshDatabase::class);

it('can get correct balances for a model', function () {

    /** @var TestCase $this */
    $user = $this->createUser();
    $user2 = $this->createUser();

    $balance = PointBalanceManager::of($user);
    $balance2 = PointBalanceManager::of($user2);

    $this->assertEquals(0, $balance->getValue());
    $this->assertEquals(0, $balance2->getValue());

    (new DepositPoints)->handle(to: $user, points: new Reward(1000));
    $this->assertEquals(1000, $balance->getValue());
    $this->assertEquals(1000, $balance->getReceivedValue());
    $this->assertEquals(0, $balance->getSentValue());

    (new ChargePoints)->handle(from: $user, points: new Reward(500));
    $this->assertEquals(500, $balance->getValue());
    $this->assertEquals(1000, $balance->getReceivedValue());
    $this->assertEquals(500, $balance->getSentValue());

    (new DepositPoints)->handle(to: $user, points: new Reward(200));
    $this->assertEquals(700, $balance->getValue());
    $this->assertEquals(1200, $balance->getReceivedValue());
    $this->assertEquals(500, $balance->getSentValue());

    (new TransferPoints)->handle(from: $user, to: $user2, points: new Reward(450));
    $this->assertEquals(250, $balance->getValue());
    $this->assertEquals(1200, $balance->getReceivedValue());
    $this->assertEquals(950, $balance->getSentValue());
    $this->assertEquals(450, $balance2->getValue());
    $this->assertEquals(450, $balance2->getReceivedValue());
    $this->assertEquals(0, $balance2->getSentValue());

})->group('rewards', 'balance', 'managers', 'balance-manager');

it('can get correct values in Numeric, Reward and int', function () {

    /** @var TestCase $this */
    $user = $this->createUser();
    $user2 = $this->createUser();

    $balance = PointBalanceManager::of($user);
    $balance2 = PointBalanceManager::of($user2);

    (new DepositPoints)->handle(to: $user, points: new Reward(1000));
    (new ChargePoints)->handle(from: $user, points: new Reward(500));
    (new TransferPoints)->handle(from: $user, to: $user2, points: new Reward(450));

    // Balance
    $this->assertEquals(new Numeric(50), $balance->get());
    $this->assertEquals(new Reward(50), $balance->getReward());
    $this->assertEquals(50, $balance->getValue());

    // Pending
    $this->assertEquals(new Numeric(0), $balance->getPending());
    $this->assertEquals(new Reward(0), $balance->getPendingReward());
    $this->assertEquals(0, $balance->getPendingValue());

    // On hold
    $this->assertEquals(new Numeric(0), $balance->getOnHold());
    $this->assertEquals(new Reward(0), $balance->getOnHoldReward());
    $this->assertEquals(0, $balance->getOnHoldValue());

    // Sent
    $this->assertEquals(new Numeric(950), $balance->getSent());
    $this->assertEquals(new Reward(950), $balance->getSentReward());
    $this->assertEquals(950, $balance->getSentValue());

    // Received
    $this->assertEquals(new Numeric(1000), $balance->getReceived());
    $this->assertEquals(new Reward(1000), $balance->getReceivedReward());
    $this->assertEquals(1000, $balance->getReceivedValue());

})->group('rewards', 'balance', 'managers', 'balance-manager');

it('can get model transactions', function () {

    /** @var TestCase $this */
    $user = $this->createUser();
    $user2 = $this->createUser();

    $balance = PointBalanceManager::of($user);

    (new DepositPoints)->handle(to: $user, points: new Reward(1000));
    (new ChargePoints)->handle(from: $user, points: new Reward(500));
    (new TransferPoints)->handle(from: $user, to: $user2, points: new Reward(450));

    $transactions = $balance->getTransactions();

    $this->assertInstanceOf(Collection::class, $transactions);
    $this->assertEquals(3, $transactions->count());

    $this->assertInstanceOf(Builder::class, $balance->getTransactionsQuery());

    $transactions = $balance->getTransactionsQuery()->limit(1)->get();

    $this->assertInstanceOf(Collection::class, $transactions);
    $this->assertEquals(1, $transactions->count());

})->group('rewards', 'balance', 'managers', 'balance-manager');

it('can check if model has enough points to perform an operation', function () {

    /** @var TestCase $this */
    $user = $this->createUser();

    $balance = PointBalanceManager::of($user);

    (new DepositPoints)->handle(to: $user, points: new Reward(200));

    $this->assertEquals(200, $balance->getValue());

    $this->assertTrue($balance->hasEnoughPoints(new Reward(100)));
    $this->assertFalse($balance->hasEnoughPoints(new Reward(300)));

})->group('rewards', 'balance', 'managers', 'balance-manager');
