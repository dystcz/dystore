<?php

use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\CartSessionInterface;

uses(TestCase::class, RefreshDatabase::class)
    ->group('carts', 'payment_options');

beforeEach(function () {
    /** @var TestCase $this */
    $this->cartSession = App::make(CartSessionInterface::class);
});

test('users can unset a payment option from cart', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->create([
        'payment_option' => 'paypal',
    ]);

    $this->cartSession->use($cart);

    $response = $this
        ->jsonApi()
        ->expects('carts')
        ->withData([
            'type' => 'carts',
        ])
        ->post(serverUrl('/carts/-actions/unset-payment-option'));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($cart);

    $this->assertDatabaseHas($cart->getTable(), [
        'payment_option' => null,
    ]);

    expect($cart->fresh()->payment_option)->toBeNull();
});

it('can unset a hidden payment option to a cart', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->create([
        'payment_option' => 'bank-transfer',
    ]);

    $this->cartSession->use($cart);

    $response = $this
        ->jsonApi()
        ->expects('carts')
        ->withData([
            'type' => 'carts',
        ])
        ->post(serverUrl('/carts/-actions/unset-payment-option'));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($cart);

    $this->assertDatabaseHas($cart->getTable(), [
        'payment_option' => null,
    ]);

    expect($cart->fresh()->payment_option)->toBeNull();
});
