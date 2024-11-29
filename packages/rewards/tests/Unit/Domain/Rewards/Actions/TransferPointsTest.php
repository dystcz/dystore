<?php

use Dystcz\LunarRewards\Domain\Rewards\Actions\DepositPoints;
use Dystcz\LunarRewards\Domain\Rewards\Actions\TransferPoints;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Domain\Rewards\Managers\PointBalanceManager;
use Dystcz\LunarRewards\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use O21\LaravelWallet\Exception\InsufficientFundsException;

uses(TestCase::class, RefreshDatabase::class);

it('can transfer correct amount of points between models', function () {

    /** @var TestCase $this */
    $user = $this->createUser();
    $user2 = $this->createUser();

    (new DepositPoints)->handle(to: $user, points: new Reward(1000));
    (new DepositPoints)->handle(to: $user2, points: new Reward(200));

    $balance = PointBalanceManager::of($user);
    $balance2 = PointBalanceManager::of($user2);

    (new TransferPoints)->handle(from: $user, to: $user2, points: new Reward(200)); // 800, 400
    (new TransferPoints)->handle(from: $user2, to: $user, points: new Reward(100)); // 900, 300
    (new TransferPoints)->handle(from: $user, to: $user2, points: new Reward(150)); // 750, 450
    (new TransferPoints)->handle(from: $user, to: $user2, points: new Reward(50)); // 700, 500
    (new TransferPoints)->handle(from: $user2, to: $user, points: new Reward(20)); // 720, 480

    // User
    $this->assertEquals(720, $balance->getValue());
    $this->assertEquals(400, $balance->getSentValue());
    $this->assertEquals(1000 + 120, $balance->getReceivedValue());

    // User 2
    $this->assertEquals(480, $balance2->getValue());
    $this->assertEquals(120, $balance2->getSentValue());
    $this->assertEquals(200 + 400, $balance2->getReceivedValue());

})->group('rewards', 'balance', 'transfer');

it('it throws an exception if there are not enough funds to charge', function () {

    /** @var TestCase $this */
    $user = $this->createUser();
    $user2 = $this->createUser();

    (new DepositPoints)->handle(to: $user, points: new Reward(1000));

    $this->assertThrows(
        fn () => (new TransferPoints)->handle(from: $user, to: $user2, points: new Reward(1200)),
        InsufficientFundsException::class,
    );

})->group('rewards', 'balance', 'charge');
