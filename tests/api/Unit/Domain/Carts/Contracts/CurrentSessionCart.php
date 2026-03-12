<?php

use Dystore\Api\Domain\Carts\Contracts\CurrentSessionCart;
use Dystore\Api\Domain\Carts\Factories\CartFactory;
use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Base\CartSessionInterface;
use Lunar\Managers\CartSessionManager;

uses(TestCase::class, RefreshDatabase::class);

it('can get current cart from session when resolving contract', function () {
    /** @var Cart $cart */
    $carts = CartFactory::new()
        ->withAddresses()
        ->withLines()
        ->count(3)
        ->create();

    $sessionCart = array_rand($carts);

    /** @var CartSessionManager $cartSession */
    $cartSession = App::make(CartSessionInterface::class);

    $cartSession->use($sessionCart);

    /** @var TestCase $this */
    $cart = App::make(CurrentSessionCart::class);

    $this->assertInstanceOf(Cart::class, $sessionCart);

})->group('carts', 'carts.model');
