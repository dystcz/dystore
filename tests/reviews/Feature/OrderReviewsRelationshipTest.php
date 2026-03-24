<?php

use Carbon\Carbon;
use Dystore\Api\Base\Enums\PublishedStatus;
use Dystore\Api\Domain\Orders\Models\Order;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Tests\Reviews\Stubs\Users\User;
use Dystore\Tests\Reviews\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('reviews');

it('can list order reviews', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $order = Order::factory()
        ->for($user)
        ->create();

    $review = Review::factory()
        ->for($order, 'purchasable')
        ->state([
            'status' => PublishedStatus::PUBLISHED,
            'published_at' => Carbon::now(),
        ])
        ->create();

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/orders/{$order->getRouteKey()}/reviews"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany([$review]);
});

it('can list multiple order reviews', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $order = Order::factory()
        ->for($user)
        ->create();

    $reviews = Review::factory()
        ->for($order, 'purchasable')
        ->count(3)
        ->create([
            'status' => PublishedStatus::PUBLISHED,
            'published_at' => Carbon::now(),
        ]);

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/orders/{$order->getRouteKey()}/reviews"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($reviews);
});

it('does not return reviews from other orders', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $order = Order::factory()
        ->for($user)
        ->create();

    $otherOrder = Order::factory()
        ->for($user)
        ->create();

    $review = Review::factory()
        ->for($order, 'purchasable')
        ->state([
            'status' => PublishedStatus::PUBLISHED,
            'published_at' => Carbon::now(),
        ])
        ->create();

    $otherReview = Review::factory()
        ->for($otherOrder, 'purchasable')
        ->state([
            'status' => PublishedStatus::PUBLISHED,
            'published_at' => Carbon::now(),
        ])
        ->create();

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('reviews')
        ->get(serverUrl("/orders/{$order->getRouteKey()}/reviews"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany([$review]);
});
