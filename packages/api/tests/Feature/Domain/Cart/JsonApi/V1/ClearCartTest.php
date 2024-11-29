<?php

use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\Currencies\Models\Currency;
use Dystore\Api\Domain\Prices\Models\Price;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\CartSessionInterface;
use Lunar\Managers\CartSessionManager;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
    $currency = Currency::getDefault();

    $cart = Cart::factory()->create([
        'currency_id' => $currency->id,
    ]);

    $purchasable = ProductVariant::factory()->create([
        'unit_quantity' => 1,
    ]);

    Price::factory()->create([
        'price' => 100,
        'min_quantity' => 1,
        'currency_id' => $currency->id,
        'priceable_type' => $purchasable->getMorphClass(),
        'priceable_id' => $purchasable->id,
    ]);

    $cart->lines()->create([
        'purchasable_type' => $purchasable->getMorphClass(),
        'purchasable_id' => $purchasable->id,
        'quantity' => 1,
    ]);

    /** @property CartSessionManager $cartSession */
    $this->cartSession = App::make(CartSessionInterface::class);

    $this->cartSession->use($cart);
});

it('can clear the cart which is currently in session', function () {
    /** @var TestCase $this */
    expect($this->cartSession->current()->lines->count())->toBe(1);

    $response = $this
        ->jsonApi()
        ->delete('/api/v1/carts/-actions/clear');

    $response->assertNoContent();

    expect($this->cartSession->current()->lines->count())->toBe(0);
})->group('carts');
