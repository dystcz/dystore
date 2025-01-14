<?php

use Dystore\Api\Domain\Orders\Models\Order;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('orders');

it('can update order by its owner', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @var Order $order */
    $order = Order::factory()->for($user)->create();

    $data = [
        'type' => 'orders',
        'id' => (string) $order->getRouteKey(),
        'attributes' => [
            'notes' => 'This is a note.',
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->withData($data)
        ->patch(serverUrl('/orders/').$order->getRouteKey());

    $response->assertFetchedOne($order);
});

it('can update order meta', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @var Order $order */
    $order = Order::factory()->for($user)->create();

    $data = [
        'type' => 'orders',
        'id' => (string) $order->getRouteKey(),
        'attributes' => [
            'meta' => [
                'packeta_id' => 123456789,
            ],
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->withData($data)
        ->patch(serverUrl('/orders/').$order->getRouteKey());

    $response->assertFetchedOne($order);

    $this->assertDatabaseHas((new Order)->getTable(), [
        'meta' => json_encode(['packeta_id' => 123456789]),
    ]);
});

it('cannot update order by other user', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @var Order $order */
    $order = Order::factory()->create();

    $data = [
        'type' => 'orders',
        'id' => (string) $order->getRouteKey(),
        'attributes' => [
            'notes' => 'This is a note.',
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->withData($data)
        ->patch(serverUrl('/orders/').$order->getRouteKey());

    $response->assertErrorStatus([
        'detail' => 'This action is unauthorized.',
        'status' => '403',
        'title' => 'Forbidden',
    ]);
});
