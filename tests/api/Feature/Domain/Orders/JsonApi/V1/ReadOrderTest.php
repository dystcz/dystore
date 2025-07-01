<?php

use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\Checkout\Enums\CheckoutProtectionStrategy;
use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\Orders\Models\Order;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Lunar\Base\CartSessionInterface;
use Lunar\Managers\CartSessionManager;

uses(TestCase::class, RefreshDatabase::class)
    ->group('orders');

beforeEach(function () {
    /** @var TestCase $this */
    $this->user = User::factory()
        ->has(Customer::factory())
        ->create();

    $this->cart = Cart::factory()
        ->for($this->user)
        ->withAddresses()
        ->withLines(2)
        ->create();

    /** @property CartSessionManager $cartSession */
    $this->cartSession = App::make(CartSessionInterface::class);

    $this->cartSession->use($this->cart);
});

it('can show order details without signature when user is logged in and owns the order', function () {
    /** @var TestCase $this */
    $order = $this->cart->createOrder();

    $order = Order::query()
        ->where($order->getKeyName(), $order->getKey())
        ->first();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->includePaths(
            'product_lines.purchasable.product',
            'product_lines.purchasable.price',
            'product_lines.purchasable.images',
            'product_lines.currency',
            'customer',
            'order_addresses',
        )
        ->expects('orders')
        ->get(serverUrl('/orders/'.$order->getRouteKey()));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($order)
        ->assertIsIncluded('order_lines', $order->lines->first());
});

it('can show order details when accessing order with valid signature', function () {
    /** @var TestCase $this */
    Config::set('dystore.general.checkout.checkout_protection_strategy', CheckoutProtectionStrategy::SIGNATURE);

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->withData([
            'type' => 'carts',
            'attributes' => [
                'agree' => true,
                'create_user' => false,
            ],
        ])
        ->post(serverUrl('/carts/-actions/checkout'));

    $signedUrl = $response->json('data.links')['self.signed'];

    $order = Order::query()
        ->where('cart_id', $this->cart->getKey())
        ->first();

    $response = $this
        ->jsonApi()
        ->expects('orders')
        ->get($signedUrl);

    $signedUrls = [
        'self.signed' => $response->json('data.links')['self.signed'],
        'create-payment-intent.signed' => $response->json('data.links')['create-payment-intent.signed'],
        'mark-order-pending-payment.signed' => $response->json('data.links')['mark-order-pending-payment.signed'],
        'mark-order-awaiting-payment.signed' => $response->json('data.links')['mark-order-awaiting-payment.signed'],
        'check-order-payment-status.signed' => $response->json('data.links')['check-order-payment-status.signed'],
    ];

    $response
        ->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'orders',
            'id' => (string) $order->getRouteKey(),
            'links' => [
                'self' => $response->json('data.links.self'),
                ...$signedUrls,
            ],
        ]);
});

it('can show order pricing correctly', function () {
    /** @var TestCase $this */
    $order = $this->cart->createOrder();

    $order = Order::query()
        ->where($order->getKeyName(), $order->getKey())
        ->first();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('orders')
        ->get(serverUrl('/orders/'.$order->getRouteKey()));

    ray($response->json('data.attributes.prices.sub_total'));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($order);
});

it('returns unauthorized if the user does not own the order', function () {
    /** @var TestCase $this */
    $order = $this->cart->createOrder();

    $order = Order::query()
        ->where($order->getKeyName(), $order->getKey())
        ->first();

    $response = $this
        ->jsonApi()
        ->includePaths('product_lines')
        ->expects('orders')
        ->get(serverUrl("/orders/{$order->getRouteKey()}"));

    $response->assertErrorStatus([
        'detail' => 'Unauthenticated.',
        'status' => '401',
        'title' => 'Unauthorized',
    ]);
})->group('orders', 'policies');
