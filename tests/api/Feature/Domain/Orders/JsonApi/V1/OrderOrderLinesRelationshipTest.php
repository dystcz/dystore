<?php

use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\OrderLines\Models\OrderLine;
use Dystore\Api\Domain\Orders\Models\Order;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\CartSessionInterface;

uses(TestCase::class, RefreshDatabase::class)
    ->group('orders', 'order_lines');

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

it('can list related order lines', function () {
    /** @var TestCase $this */
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
        ->with(['lines'])
        ->first();

    $included = $order->productLines->map(fn (OrderLine $line) => [
        'type' => 'order_lines',
        'id' => (string) $line->getRouteKey(),
    ])->all();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('orders')
        ->includePaths('product_lines')
        ->get("{$signedUrl}");

    $response
        ->assertSuccessful()
        ->assertFetchedOne($order)
        ->assertIncluded($included);
})->todo();

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
