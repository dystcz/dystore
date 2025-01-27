<?php

use Dystore\Api\Domain\Carts\Actions\CheckoutCart;
use Dystore\Api\Domain\Carts\Contracts\CheckoutCart as CheckoutCartContract;
use Dystore\Api\Domain\Carts\Events\CartCheckedOut;
use Dystore\Api\Domain\Carts\Factories\CartFactory;
use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Lunar\Base\CartSessionInterface;
use Lunar\Managers\CartSessionManager;

uses(TestCase::class, RefreshDatabase::class)
    ->group('carts', 'carts.events', 'events');

it('dispatches cart checked out event after checkout', function () {
    /** @var TestUser $this */
    Event::fake([
        CartCheckedOut::class,
    ]);

    $user = User::factory()->create();

    /** @var CartFactory $cartFactory */
    $cartFactory = Cart::factory();

    /** @var Cart $cart */
    $cart = $cartFactory
        ->withAddresses()
        ->withLines()
        ->create();

    /** @var CartSessionManager $cartSession */
    $cartSession = App::make(CartSessionInterface::class);
    $cartSession->use($cart);

    /** @var CheckoutCart $checkoutAction */
    $checkoutAction = App::make(CheckoutCartContract::class);

    $order = $checkoutAction->handle($cart);

    Event::assertDispatched(
        CartCheckedOut::class,
        fn (CartCheckedOut $event) => $event->cart->getKey() === $cart->getKey()
        && $event->order->getKey() === $order->getKey(),
    );
});
