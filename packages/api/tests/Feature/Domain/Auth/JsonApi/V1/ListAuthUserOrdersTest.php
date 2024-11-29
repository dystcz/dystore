<?php

use Dystore\Api\Domain\Orders\Models\Order;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('auth', 'users', 'me');

it('can list all of auth users orders', function () {
    /** @var TestUser $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    $order = Order::factory()->for($user)->create();

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->includePaths(
            'product_lines',
            'product_lines.purchasable',
            'product_lines.purchasable.prices',
            'product_lines.purchasable.images',
            'product_lines.purchasable.product',
        )
        ->get(serverUrl('/auth/-actions/me/orders'));

    $response->assertFetchedMany([$order]);
});

it('can find auth users order by ref number', function () {
    /** @var TestUser $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    $order = Order::factory()->for($user)->create();

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->includePaths(
            'customer',
            'product_lines',
            'product_lines.purchasable',
            'product_lines.purchasable.prices',
            'product_lines.purchasable.images',
            'product_lines.purchasable.product',
        )
        ->filter(['reference' => $order->reference])
        ->get(serverUrl('/auth/-actions/me/orders'));

    $response->assertFetchedMany([$order]);
});
