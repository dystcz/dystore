<?php

use Dystore\Api\Domain\Addresses\Http\Enums\AddressType;
use Dystore\Api\Domain\CartAddresses\Models\CartAddress;
use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Lunar\Base\CartSessionInterface;
use Lunar\Facades\ShippingManifest;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
    $this->cartSession = App::make(CartSessionInterface::class);

    Config::set('lunar.cart_session.auto_create', false);
})->group('carts', 'shipping_options');

afterEach(function () {
    /** @var TestCase $this */
    $this->cartSession->forget();
});

it('can unset a shipping option from current session cart', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->create();

    $shippingOption = ShippingManifest::getOptions($cart)->first();

    $cartAddress = CartAddress::factory()->for($cart)->create([
        'shipping_option' => $shippingOption->identifier,
    ]);

    $this->cartSession->use($cart);

    $data = [
        'type' => 'carts',
        'attributes' => [
            'address_type' => AddressType::SHIPPING,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('cart_addresses')
        ->withData($data)
        ->post(serverUrl('/carts/-actions/unset-shipping-option'));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($cart);

    $this->assertDatabaseHas($cartAddress->getTable(), [
        'id' => $cartAddress->getRouteKey(),
        'shipping_option' => null,
    ]);

    expect($cartAddress->fresh()->shipping_option)->toBe(null);
});

it('validates cart address type and shipping option attribute when unsetting shipping option from a cart', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->create();

    $this->cartSession->use($cart);

    $data = [
        'type' => 'carts',
        'attributes' => [
            'address_type' => null,
            'shipping_option' => null,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('cart_addresses')
        ->withData($data)
        ->post(serverUrl('/carts/-actions/unset-shipping-option'));

    $response->assertErrors(422, [
        [
            'detail' => __('dystore::validations.cart_addresses.address_type.required'),
            'status' => '422',
        ],
    ]);
});

it('throws authorization exception when cart does not exist in session', function () {
    /** @var TestCase $this */
    $data = [
        'type' => 'carts',
        'attributes' => [
            'address_type' => AddressType::SHIPPING,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('cart_addresses')
        ->withData($data)
        ->post(serverUrl('/carts/-actions/unset-shipping-option'));

    $response->assertErrorStatus([
        'detail' => 'This action is unauthorized.',
        'status' => '403',
        'title' => 'Forbidden',
    ]);
});
