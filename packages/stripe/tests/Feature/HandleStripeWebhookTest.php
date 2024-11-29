<?php

use Dystcz\LunarApi\Domain\Carts\Events\CartCreated;
use Dystcz\LunarApi\Domain\Carts\Models\Cart;
use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentCanceled;
use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentFailed;
use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentSuccessful;
use Dystcz\LunarApiStripeAdapter\Jobs\Webhooks\HandlePaymentIntentSucceeded;
use Dystcz\LunarApiStripeAdapter\StripePaymentAdapter;
use Dystcz\LunarApiStripeAdapter\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Lunar\Stripe\Concerns\ConstructsWebhookEvent;
use Stripe\PaymentIntent;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
    Event::fake(CartCreated::class);

    Config::set('lunar-api.stripe.automatic_payment_methods', false);

    /** @var Cart $cart */
    $cart = Cart::factory()
        ->withAddresses()
        ->withLines()
        ->create();

    $order = $cart->createOrder();

    $intentId = App::make(StripePaymentAdapter::class)->createIntent($cart)->getId();

    $this->app->bind(ConstructsWebhookEvent::class, function ($app) {
        return new class implements ConstructsWebhookEvent
        {
            public function constructEvent(string $jsonPayload, string $signature, string $secret): Stripe\Event
            {
                return \Stripe\Event::constructFrom([]);
            }
        };
    });

    $this->intent = PaymentIntent::retrieve($intentId);

    $this->cart = $cart;
    $this->order = $order;

});

it('can handle payment_intent.succeeded event', function () {
    /** @var TestCase $this */
    Event::fake();
    Queue::fake();

    $data = json_decode(file_get_contents(__DIR__.'/../Stubs/Stripe/payment_intent.succeeded.json'), true);

    $data['data']['object']['id'] = $this->cart->meta['payment_intent'];

    PaymentIntent::update($this->intent->id, [
        // 'automatic_payment_methods' => false,
        'payment_method_types' => ['card'],
        'payment_method' => 'pm_card_visa',
    ]);

    $this->intent->confirm();

    $response = $this
        ->post(
            '/stripe/webhook',
            $data,
            ['Stripe-Signature' => $this->determineStripeSignature($data)],
        );

    $response->assertSuccessful();

    // Queue::assertPushed(HandlePaymentIntentSucceeded::class);
    // Event::assertDispatched(OrderPaymentSuccessful::class);
})->group('webhooks');

it('can handle payment_intent.cancelled event', function () {
    /** @var TestCase $this */
    Event::fake();
    Queue::fake();

    $data = json_decode(file_get_contents(__DIR__.'/../Stubs/Stripe/payment_intent.canceled.json'), true);

    $paymentIntentId = $this->cart->meta['payment_intent'];
    $data['data']['object']['id'] = $paymentIntentId;

    $response = $this
        ->post(
            '/stripe/webhook',
            $data,
            ['Stripe-Signature' => $this->determineStripeSignature($data)],
        );

    $response->assertSuccessful();

    // Event::assertDispatched(OrderPaymentCanceled::class);
})->group('webhooks');

it('can handle payment_intent.failed event', function () {
    /** @var TestCase $this */
    Event::fake();
    Queue::fake();

    $data = json_decode(file_get_contents(__DIR__.'/../Stubs/Stripe/payment_intent.payment_failed.json'), true);

    $data['data']['object']['id'] = $this->cart->meta['payment_intent'];

    $response = $this
        ->post(
            '/stripe/webhook',
            $data,
            ['Stripe-Signature' => $this->determineStripeSignature($data)],
        );

    $response->assertSuccessful();

    // Event::assertDispatched(OrderPaymentFailed::class);
})->group('webhooks');

it('can handle any other event', function () {
    /** @var TestCase $this */
    Event::fake();
    Queue::fake();

    $data = json_decode(file_get_contents(__DIR__.'/../Stubs/Stripe/charge.succeeded.json'), true);

    $data['data']['object']['id'] = $this->cart->meta['payment_intent'];

    $response = $this
        ->post(
            '/stripe/webhook',
            $data,
            ['Stripe-Signature' => $this->determineStripeSignature($data)],
        );

    $response->assertSuccessful();
})->group('webhooks');
