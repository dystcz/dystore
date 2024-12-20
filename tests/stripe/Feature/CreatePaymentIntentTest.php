<?php

use Dystore\Api\Domain\Carts\Events\CartCreated;
use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Tests\Stripe\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Lunar\Facades\CartSession;
use Lunar\Models\Transaction;

uses(TestCase::class, RefreshDatabase::class)
    ->group('payment-intents');

beforeEach(function () {
    /** @var TestCase $this */
    Event::fake(CartCreated::class);

    /** @var Cart $cart */
    $cart = Cart::factory()
        ->withAddresses()
        ->withLines()
        ->create();

    CartSession::use($cart);

    $this->order = $cart->createOrder();
    $this->cart = $cart;
});

test('can create a payment intent', function (string $paymentMethod) {
    /** @var TestCase $this */
    $url = URL::signedRoute(
        'v1.orders.createPaymentIntent',
        ['order' => $this->order->getRouteKey()],
    );

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->withData([
            'type' => 'orders',
            'id' => (string) $this->order->getRouteKey(),
            'attributes' => [
                'payment_method' => $paymentMethod,
            ],
        ])
        ->post($url);

    $response->assertSuccessful();

    // Stores the payment intent in the cart
    expect($response->json('meta.payment_intent.id'))
        ->toBe($this->cart->fresh()->paymentIntents->first()->intent_id);

})->with(['stripe']);

it('creates a transaction when creating a payement intent', function (string $paymentMethod) {
    /** @var TestCase $this */
    $url = URL::signedRoute(
        'v1.orders.createPaymentIntent',
        ['order' => $this->order->getRouteKey()],
    );

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->withData([
            'type' => 'orders',
            'id' => (string) $this->order->getRouteKey(),
            'attributes' => [
                'payment_method' => $paymentMethod,
            ],
        ])
        ->post($url);

    $response->assertSuccessful();

    $this->assertDatabaseHas((new Transaction)->getTable(), [
        'order_id' => $this->order->getRouteKey(),
        'success' => true,
        'type' => 'intent',
        'driver' => 'stripe',
        'amount' => $this->order->total,
        'reference' => $response->json('meta.payment_intent.id'),
    ]);

})->with(['stripe']);
