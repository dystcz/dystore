<?php

use Dystcz\LunarApi\Domain\Carts\Models\Cart;
use Dystcz\LunarApi\Domain\Orders\Enums\OrderStatus;
use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentFailed;
use Dystcz\LunarApi\Domain\Payments\Listeners\HandleFailedPayment;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentAdaptersRegister;
use Dystcz\LunarApi\Domain\Transactions\Models\Transaction;
use Dystcz\LunarApi\Tests\Stubs\Payments\PaymentAdapters\TestPaymentAdapter;
use Dystcz\LunarApi\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
    /** @var Cart $cart */
    $cart = Cart::factory()
        ->withAddresses()
        ->withLines()
        ->create();

    $this->order = $cart->createOrder();
    $this->cart = $cart;

    TestPaymentAdapter::register();
});

it('can handle failed payment', function () {
    /** @var TestCase $this */
    Event::fake();

    /** @var TestPaymentAdapter $paymentAdapter */
    $paymentAdapter = App::make(PaymentAdaptersRegister::class)->get('test');

    $paymentIntent = $paymentAdapter->createIntent($this->cart);

    OrderPaymentFailed::dispatch($this->order, $paymentAdapter, $paymentIntent);

    Event::assertListening(
        OrderPaymentFailed::class,
        HandleFailedPayment::class
    );

    $this->assertDatabaseHas((new Transaction)->getTable(), [
        'order_id' => $this->order->id,
        'reference' => $paymentIntent->getId(),
        'status' => $paymentIntent->getStatus(),
    ]);

    $this->assertDatabaseHas($this->order->getTable(), [
        'status' => OrderStatus::AWAITING_PAYMENT->value,
    ]);

})->group('payments');
