<?php

use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
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
        ->withLines()
        ->create();

    /** @var CartSessionInterface $cartSession */
    $this->cartSession = App::make(CartSessionInterface::class);
    $this->cartSession->use($this->cart);
});

it('can attach a customer to a cart', function () {
    /** @var TestCase $this */
    $customer = Customer::factory()->create();

    // The cart already has a user; Lunar requires the customer to be linked to that user.
    $customer->users()->attach($this->user);

    $payload = [
        'type' => 'customers',
        'id' => (string) $customer->getRouteKey(),
    ];

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('customers')
        ->withData($payload)
        ->patch(serverUrl("/carts/{$this->cart->getRouteKey()}/relationships/customer"));

    $response->assertSuccessful();

    $this->assertDatabaseHas((new Cart)->getTable(), [
        'id' => $this->cart->getRouteKey(),
        'customer_id' => $customer->getRouteKey(),
    ]);
});

it('can fetch a cart with customer included', function () {
    /** @var TestCase $this */
    $customer = Customer::factory()->create();

    $this->cart->setCustomer($customer);

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('carts')
        ->includePaths('customer')
        ->get(serverUrl("/carts/{$this->cart->getRouteKey()}"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($this->cart->fresh())
        ->assertIncluded([
            [
                'type' => 'customers',
                'id' => (string) $customer->getRouteKey(),
            ],
        ]);
});
