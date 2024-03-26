<?php

use Dystcz\LunarApi\Domain\Carts\Models\Cart;
use Dystcz\LunarApi\Domain\Carts\Pipelines\ApplyPayment;
use Dystcz\LunarApi\Domain\PaymentOptions\Data\PaymentOption;
use Dystcz\LunarApi\Domain\PaymentOptions\Facades\PaymentManifest;
use Dystcz\LunarApi\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Lunar\DataTypes\Price as PriceDataType;
use Lunar\Models\Currency;
use Lunar\Models\Price;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRateAmount;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    Config::set('lunar.payments.default', 'paypal');
});

it('can apply empty payment totals', function () {
    /** @var TestCase $this */
    $currency = Currency::factory()->create();

    /** @var Cart $cart */
    $cart = Cart::factory()->create([
        'currency_id' => $currency->id,
    ]);

    $purchasable = ProductVariant::factory()->create();

    Price::factory()->create([
        'price' => 100,
        'tier' => 1,
        'currency_id' => $currency->id,
        'priceable_type' => get_class($purchasable),
        'priceable_id' => $purchasable->id,
    ]);

    $cart->lines()->create([
        'purchasable_type' => get_class($purchasable),
        'purchasable_id' => $purchasable->id,
        'quantity' => 1,
    ]);

    $this->assertNull($cart->paymentTotal);

    $option = PaymentManifest::getOption($cart, 'PAYPAL');

    $cart = $cart->setPaymentOption($option);

    app(ApplyPayment::class)->handle($cart, function ($cart) {
        return $cart;
    });

    $this->assertInstanceOf(PriceDataType::class, $cart->paymentSubTotal);
})->group('lunar-api.cart.payment-options');

it('can apply payment totals', function () {
    /** @var TestCase $this */
    $currency = Currency::factory()->create();

    $cart = Cart::factory()->create([
        'currency_id' => $currency->id,
    ]);

    $taxClass = TaxClass::factory()->create([
        'name' => 'Foobar',
    ]);

    $taxClass->taxRateAmounts()->create(
        TaxRateAmount::factory()->make([
            'percentage' => 20,
            'tax_class_id' => $taxClass->id,
        ])->toArray()
    );

    $paymentOption = new PaymentOption(
        name: 'Stripe',
        driver: 'stripe',
        description: 'Stripe payment option',
        identifier: 'STRIPE',
        price: new PriceDataType(500, $cart->currency, 1),
        taxClass: $taxClass
    );

    PaymentManifest::addOption($paymentOption);

    $cart->update([
        'payment_option' => $paymentOption->getIdentifier(),
    ]);

    $purchasable = ProductVariant::factory()->create();

    Price::factory()->create([
        'price' => 100,
        'tier' => 1,
        'currency_id' => $currency->id,
        'priceable_type' => get_class($purchasable),
        'priceable_id' => $purchasable->id,
    ]);

    $cart->lines()->create([
        'purchasable_type' => get_class($purchasable),
        'purchasable_id' => $purchasable->id,
        'quantity' => 1,
    ]);

    $this->assertNull($cart->paymentTotal);

    app(ApplyPayment::class)->handle($cart, function ($cart) {
        return $cart;
    });

    $this->assertInstanceOf(PriceDataType::class, $cart->paymentSubTotal);
    $this->assertEquals(500, $cart->paymentSubTotal->value);
})->group('lunar-api.cart.payment-options');