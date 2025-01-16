<?php

use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\PaymentOptions\Entities\PaymentOption;
use Dystore\Api\Domain\PaymentOptions\Facades\PaymentManifest;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\CartSessionInterface;

uses(TestCase::class, RefreshDatabase::class)
    ->group('carts', 'payment_options');

beforeEach(function () {
    /** @var TestCase $this */
    $this->cartSession = App::make(CartSessionInterface::class);

    $this->cart = Cart::factory()->create();

    $this->paymentOption = PaymentManifest::getOptions($this->cart)->first();
});

test('users can set a payment option to cart', function () {
    /** @var TestCase $this */
    $this->cartSession->use($this->cart);

    $data = [
        'type' => 'carts',
        'attributes' => [
            'payment_option' => $this->paymentOption->identifier,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('carts')
        ->withData($data)
        ->post(serverUrl('/carts/-actions/set-payment-option'));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($this->cart);

    $this->assertDatabaseHas($this->cart->getTable(), [
        'payment_option' => $this->paymentOption->identifier,
    ]);

    expect($this->cart->fresh()->payment_option)->toBe($data['attributes']['payment_option']);
});

it('validates payment option attribute when setting payment option to a cart', function () {
    /** @var TestCase $this */
    $response = $this
        ->jsonApi()
        ->expects('carts')
        ->withData([
            'type' => 'carts',
            'attributes' => [
                'payment_option' => null,
            ],
        ])
        ->post(serverUrl('/carts/-actions/set-payment-option'));

    $response->assertErrorStatus([
        'detail' => __('dystore::validations.payments.set_payment_option.payment_option.required'),
        'status' => '422',
    ]);
});

it('can set a hidden payment option to a cart', function () {
    /** @var TestCase $this */
    $hiddenOption = PaymentManifest::getOptions($this->cart, true)
        ->firstWhere(fn (PaymentOption $option) => $option->isHidden());

    $this->cartSession->use($this->cart);

    $data = [
        'type' => 'carts',
        'attributes' => [
            'payment_option' => $hiddenOption->identifier,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('carts')
        ->withData($data)
        ->post(serverUrl('/carts/-actions/set-payment-option'));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($this->cart);

    $this->assertDatabaseHas($this->cart->getTable(), [
        'payment_option' => $hiddenOption->identifier,
    ]);

    expect($this->cart->fresh()->payment_option)->toBe($data['attributes']['payment_option']);
});
