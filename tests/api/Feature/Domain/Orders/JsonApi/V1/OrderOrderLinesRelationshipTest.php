<?php

use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\OrderLines\Models\OrderLine;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Lunar\Base\CartSessionInterface;

uses(TestCase::class, RefreshDatabase::class)
    ->group('orders', 'order_lines');

beforeEach(function () {
    Config::set('lunar.pricing.stored_inclusive_of_tax', true);
    /** @var TestCase $this */
    $this->user = User::factory()
        ->has(Customer::factory())
        ->create();

    $this->cart = Cart::factory()
        ->for($this->user)
        ->withAddresses()
        ->withLines()
        ->create();

    /** @property CartSessionManager $cartSession */
    $this->cartSession = App::make(CartSessionInterface::class);

    $this->cartSession->use($this->cart);
});

it('can list related product lines', function () {
    /** @var TestCase $this */
    $order = $this->cart->createOrder();

    $order->load(['lines']);

    $expected = $order->productLines->map(fn (OrderLine $line) => [
        'id' => (string) $line->getRouteKey(),
        'type' => 'order_lines',
        'attributes' => [
            'purchasable_type' => $line->purchasable_type,
            'purchasable_id' => $line->purchasable_id,
            'type' => $line->type,
        ],
    ])->all();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('orders')
        ->get(serverUrl("/orders/{$order->getRouteKey()}/product_lines"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($expected);
});

it('returns correct product line pricing', function () {
    /** @var TestCase $this */
    $order = $this->cart->createOrder();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('orders')
        ->get(serverUrl("/orders/{$order->getRouteKey()}/product_lines"));

    $response
        ->assertSuccessful();

    ray($response->json('data')[0]['attributes']['pricing']);
});

it('cannot list order lines relationships without url signature', function () {
    /** @var TestCase $this */
    $order = $this->cart->createOrder();

    $response = $this
        ->jsonApi()
        ->expects('order_lines')
        ->get(serverUrl("/orders/{$order->getRouteKey()}/relationships/order_lines"));

    $response->assertErrorStatus([
        'detail' => 'Unauthenticated.',
        'status' => '401',
        'title' => 'Unauthorized',
    ]);

})->group('orders', 'order_lines', 'policies');
