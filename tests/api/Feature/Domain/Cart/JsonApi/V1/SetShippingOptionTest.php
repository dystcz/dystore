<?php

use Dystore\Api\Domain\Addresses\Http\Enums\AddressType;
use Dystore\Api\Domain\CartAddresses\Models\CartAddress;
use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Tests\Api\TestCase;
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

test('users can set a shipping option to current session cart', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->create();

    $cartAddress = CartAddress::factory()->for($cart)->create();

    $shippingOption = ShippingManifest::getOptions($cart)->first();

    $this->cartSession->use($cart);

    $data = [
        'type' => 'carts',
        'attributes' => [
            'address_type' => AddressType::SHIPPING,
            'shipping_option' => $shippingOption->identifier,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('cart_addresses')
        ->withData($data)
        ->post(serverUrl('/carts/-actions/set-shipping-option'));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($cart);

    $this->assertDatabaseHas($cartAddress->getTable(), [
        'id' => $cartAddress->getRouteKey(),
        'shipping_option' => $shippingOption->identifier,
    ]);

    expect($cartAddress->fresh()->shipping_option)->toBe($data['attributes']['shipping_option']);
});

it('validates cart address type and shipping option attribute when setting shipping option to cart', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->create();

    $cartAddress = CartAddress::factory()->for($cart)->create();

    $shippingOption = ShippingManifest::getOptions($cart)->first();

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
        ->post(serverUrl('/carts/-actions/set-shipping-option'));

    $response->assertErrors(422, [
        [
            'detail' => __('dystore::validations.shipping.set_shipping_option.shipping_option.required'),
            'status' => '422',
        ],
        [
            'detail' => __('dystore::validations.cart_addresses.address_type.required'),
            'status' => '422',
        ],
    ]);
});

it('throws authorization exception when cart does not exist in session', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->create();

    $cartAddress = CartAddress::factory()->for($cart)->create();

    $shippingOption = ShippingManifest::getOptions($cart)->first();

    $data = [
        'type' => 'carts',
        'attributes' => [
            'address_type' => AddressType::SHIPPING,
            'shipping_option' => $shippingOption->identifier,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('cart_addresses')
        ->withData($data)
        ->post(serverUrl('/carts/-actions/set-shipping-option'));

    $response->assertErrorStatus([
        'detail' => 'This action is unauthorized.',
        'status' => '403',
        'title' => 'Forbidden',
    ]);
});
