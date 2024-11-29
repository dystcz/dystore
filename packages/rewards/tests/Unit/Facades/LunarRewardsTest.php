<?php

use Dystcz\LunarRewards\Domain\Rewards\Actions\DepositPoints;
use Dystcz\LunarRewards\Domain\Rewards\DataTypes\Reward;
use Dystcz\LunarRewards\Facades\LunarRewards;
use Dystcz\LunarRewards\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(TestCase::class, RefreshDatabase::class);

it('can get balance by calling a facade', function () {

    /** @var TestCase $this */
    $user = $this->createUser();

    (new DepositPoints)->handle($user, new Reward(1000));

    $balance = LunarRewards::balanceManager($user);

    $this->assertEquals(1000, $balance->getValue());
    $this->assertEquals(1000, LunarRewards::balance($user));

})->group('rewards', 'facade');

it('can deposit points by calling a facade', function () {

    /** @var TestCase $this */
    $user = $this->createUser();

    $balance = LunarRewards::balanceManager($user);

    LunarRewards::deposit($user, new Reward(1000));

    $this->assertEquals(1000, $balance->getValue());
    $this->assertEquals(1000, LunarRewards::balance($user));

})->group('rewards', 'facade');

it('can charge points by calling a facade', function () {

    /** @var TestCase $this */
    $user = $this->createUser();

    LunarRewards::deposit($user, new Reward(500));

    $balance = LunarRewards::balanceManager($user);

    LunarRewards::charge($user, new Reward(200));

    $this->assertEquals(300, $balance->getValue());
    $this->assertEquals(300, LunarRewards::balance($user));

})->group('rewards', 'facade');

it('can transfer points by calling a facade', function () {

    /** @var TestCase $this */
    $user = $this->createUser();
    $user2 = $this->createUser();

    LunarRewards::deposit($user, new Reward(600));

    $balance = LunarRewards::balanceManager($user);
    $balance2 = LunarRewards::balanceManager($user2);

    LunarRewards::transfer($user, $user2, new Reward(400));

    $this->assertEquals(200, $balance->getValue());
    $this->assertEquals(200, LunarRewards::balance($user));

    $this->assertEquals(400, $balance2->getValue());
    $this->assertEquals(400, LunarRewards::balance($user2));

})->group('rewards', 'facade');

it('can get list of transactions by calling a facade', function () {

    /** @var TestCase $this */
    $user = $this->createUser();
    $user2 = $this->createUser();

    LunarRewards::deposit($user, new Reward(600));
    LunarRewards::charge($user, new Reward(200));
    LunarRewards::transfer($user, $user2, new Reward(100));

    $this->assertCount(3, LunarRewards::transactions($user));
    $this->assertCount(1, LunarRewards::transactions($user2));
    $this->assertInstanceOf(Collection::class, LunarRewards::transactions($user));

})->group('rewards', 'facade');
