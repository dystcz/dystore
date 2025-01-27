<?php

use Dystore\Api\Domain\Orders\Events\OrderCreated;
use Dystore\Api\Domain\Orders\Models\Order;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(TestCase::class, RefreshDatabase::class)
    ->group('orders', 'orders.events', 'events');

it('dispatches order created event after order creation', function () {
    /** @var TestUser $this */
    Event::fake([
        OrderCreated::class,
    ]);

    $user = User::factory()->create();

    $this->actingAs($user);

    $order = Order::factory()->for($user)->create();

    Event::assertDispatched(
        OrderCreated::class,
        fn (OrderCreated $event) => $event->order->getKey() === $order->getKey(),
    );
});
