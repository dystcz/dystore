<?php

use Dystore\Api\Domain\CartLines\Models\CartLine;
use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\Products\Factories\ProductFactory;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Api\Facades\LunarApi;
use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Facades\CartSession;

uses(TestCase::class, RefreshDatabase::class);

it('can add purchasable to the cart', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->withLines()->create();

    $cartLine = $cart->lines->first();

    $data = [
        'type' => 'cart_lines',
        'attributes' => [
            'quantity' => $cartLine->quantity,
            'purchasable_id' => $cartLine->purchasable_id,
            'purchasable_type' => $cartLine->purchasable_type,
            'meta' => null,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('cart_lines')
        ->withData($data)
        ->post(serverUrl('/cart_lines'));

    $id = $response
        ->assertCreatedWithServerId('http://localhost/api/v1/cart_lines', $data)
        ->id();

    if (LunarApi::usesHashids()) {
        $id = decodeHashedId($cartLine, $id);
    }

    $this->assertDatabaseHas($cartLine->getTable(), [
        'id' => $id,
        'purchasable_id' => $cartLine->purchasable_id,
        'purchasable_type' => $cartLine->purchasable_type,
        'quantity' => $cartLine->quantity,
    ]);
})->group('cart_lines');

it('can update a cart line', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->create();

    $cartLine = CartLine::factory()
        ->for(
            ProductVariantFactory::new()->for(ProductFactory::new())->withPrice(),
            'purchasable'
        )
        ->for($cart)
        ->create();

    CartSession::use($cart);

    $data = [
        'type' => 'cart_lines',
        'id' => (string) $cartLine->getRouteKey(),
        'attributes' => [
            'purchasable_id' => $cartLine->purchasable_id,
            'purchasable_type' => $cartLine->purchasable_type,
            'quantity' => 1,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('cart_lines')
        ->withData($data)
        ->patch('/api/v1/cart_lines/'.$cartLine->getRouteKey());

    $response->assertFetchedOne($cartLine);

    $this->assertDatabaseHas($cartLine->getTable(), [
        'id' => $cartLine->getKey(),
        'quantity' => $data['attributes']['quantity'],
    ]);
})->group('cart_lines');

test('only the owner of the cart can update cart lines', function () {
    /** @var TestCase $this */
    $cartLine = CartLine::factory()
        ->for(ProductVariantFactory::new(), 'purchasable')
        ->create();

    $data = [
        'type' => 'cart_lines',
        'id' => (string) $cartLine->getRouteKey(),
        'attributes' => [
            'purchasable_id' => $cartLine->purchasable_id,
            'purchasable_type' => $cartLine->purchasable_type,
            'quantity' => 1,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('cart_lines')
        ->withData($data)
        ->patch('/api/v1/cart_lines/'.$cartLine->getRouteKey());

    $response->assertErrorStatus([
        'detail' => 'Unauthenticated.',
        'status' => '401',
        'title' => 'Unauthorized',
    ]);
})->group('cart_lines', 'policies');
