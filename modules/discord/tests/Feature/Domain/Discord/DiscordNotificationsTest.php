<?php

use Domain\Carts\Models\Cart;
use Domain\Orders\Actions\SendOrderPlacedNotification;
use Domain\Orders\Notifications\OrderPlacedNotification;
use Domain\Users\Models\User;
use Dystcz\LunarApi\Domain\Carts\Events\CartCreated;
use Dystcz\LunarApi\Domain\Orders\Events\OrderCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Lunar\Base\CartSessionInterface;
use Lunar\Managers\CartSessionManager;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
    $this->user = User::factory()->create();

    /** @var CartFactory $factory */
    $factory = Cart::factory();

    /** @var Cart $cart */
    $this->cart = $factory
        ->withAddresses()
        ->withLines()
        ->create();

    /** @var CartSessionManager $cartSession */
    $cartSession = App::make(CartSessionInterface::class);
    $cartSession->use($this->cart);
});

it('sends discord notification when order is created', function () {
    /** @var TestCase $this */
    Event::fake();
    Notification::fake();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('orders')
        ->withData([
            'type' => 'carts',
            'attributes' => [
                'agree' => true,
                'create_user' => false,
            ],
        ])
        ->post('/api/v1/carts/-actions/checkout');

    dd($response->exception);

    $response
        ->assertSuccessful();

    Event::assertDispatched(CartCreated::class);
    Event::assertDispatched(OrderCreated::class);
    Event::assertListening(OrderCreated::class, SendOrderPlacedNotification::class);
    Notification::assertSentTo($this->user, OrderPlacedNotification::class);

})->group('orders', 'orders.notifications', 'orders.discord')->todo();
