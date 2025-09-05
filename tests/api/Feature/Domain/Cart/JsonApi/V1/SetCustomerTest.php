<?php

use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Lunar\Base\CartSessionInterface;

uses(TestCase::class, RefreshDatabase::class)
    ->group('carts', 'carts.customer');

beforeEach(function () {
    /** @var TestCase $this */
    $this->user = User::factory()
        ->has(Customer::factory())
        ->create();

    $this->cart = Cart::factory()
        ->for($this->user)
        ->create();

    /** @var CartSessionInterface $cartSession */
    $this->cartSession = App::make(CartSessionInterface::class);

    Config::set('lunar.cart_session.auto_create', false);
});

afterEach(function () {
    /** @var TestCase $this */
    $this->cartSession->forget();
});

test('users can set a customer to current session cart', function () {
    /** @var TestCase $this */
    $customer = $this->user->customers()->first();

    $this->cartSession->use($this->cart);

    $payload = [
        'type' => 'carts',
        'relationships' => [
            'customer' => [
                'data' => [
                    'type' => 'customers',
                    'id' => (string) $customer->getRouteKey(),
                ],
            ],
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('carts')
        ->withData($payload)
        ->post(serverUrl('/carts/-actions/set-customer'));

    $response->assertSuccessful();

    $this->assertDatabaseHas((new Cart)->getTable(), [
        'id' => $this->cart->getRouteKey(),
        'customer_id' => $customer->getRouteKey(),
    ]);
});

it('throws authorization exception when cart does not exist in session', function () {
    /** @var TestCase $this */
    $this->cartSession->forget();
    $customer = $this->user->customers()->first();

    $payload = [
        'type' => 'carts',
        'relationships' => [
            'customer' => [
                'data' => [
                    'type' => 'customers',
                    'id' => (string) $customer->getRouteKey(),
                ],
            ],
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('carts')
        ->withData($payload)
        ->post(serverUrl('/carts/-actions/set-customer'));

    $response->assertErrorStatus([
        'detail' => 'This action is unauthorized.',
        'status' => '403',
        'title' => 'Forbidden',
    ]);
});
