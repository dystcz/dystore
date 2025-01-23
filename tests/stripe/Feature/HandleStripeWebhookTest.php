<?php

use Dystore\Api\Domain\Carts\Events\CartCreated;
use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Stripe\Jobs\Webhooks\HandleOtherEvent;
use Dystore\Stripe\Jobs\Webhooks\HandlePaymentIntentCanceled;
use Dystore\Stripe\Jobs\Webhooks\HandlePaymentIntentFailed;
use Dystore\Stripe\Jobs\Webhooks\HandlePaymentIntentSucceeded;
use Dystore\Stripe\StripePaymentAdapter;
use Dystore\Tests\Stripe\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Lunar\Stripe\Concerns\ConstructsWebhookEvent;
use Stripe\PaymentIntent;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
    Event::fake(CartCreated::class);

    Config::set('dystore.stripe.automatic_payment_methods', false);

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
    Bus::fake([HandlePaymentIntentSucceeded::class]);

    $data = json_decode(file_get_contents(__DIR__.'/../Stubs/Stripe/payment_intent.succeeded.json'), true);

    $paymentIntentId = $this->cart->fresh()->paymentIntents->first()->intent_id;
    $data['data']['object']['id'] = $paymentIntentId;

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

    Bus::assertDispatched(HandlePaymentIntentSucceeded::class);
})->group('webhooks');

it('can handle payment_intent.canceled event', function () {
    /** @var TestCase $this */
    Bus::fake([HandlePaymentIntentCanceled::class]);

    $data = json_decode(file_get_contents(__DIR__.'/../Stubs/Stripe/payment_intent.canceled.json'), true);

    $paymentIntentId = $this->cart->fresh()->paymentIntents->first()->intent_id;
    $data['data']['object']['id'] = $paymentIntentId;

    $response = $this
        ->post(
            '/stripe/webhook',
            $data,
            ['Stripe-Signature' => $this->determineStripeSignature($data)],
        );

    $response->assertSuccessful();

    Bus::assertDispatched(HandlePaymentIntentCanceled::class);
})->group('webhooks');

it('can handle payment_intent.failed event', function () {
    /** @var TestCase $this */
    Bus::fake([HandlePaymentIntentFailed::class]);

    $data = json_decode(file_get_contents(__DIR__.'/../Stubs/Stripe/payment_intent.payment_failed.json'), true);

    $paymentIntentId = $this->cart->fresh()->paymentIntents->first()->intent_id;
    $data['data']['object']['id'] = $paymentIntentId;

    $response = $this
        ->post(
            '/stripe/webhook',
            $data,
            ['Stripe-Signature' => $this->determineStripeSignature($data)],
        );

    $response->assertSuccessful();

    Bus::assertDispatched(HandlePaymentIntentFailed::class);
})->group('webhooks');

it('can handle any other event', function () {
    /** @var TestCase $this */
    Bus::fake([HandleOtherEvent::class]);

    $data = json_decode(file_get_contents(__DIR__.'/../Stubs/Stripe/charge.succeeded.json'), true);

    $paymentIntentId = $this->cart->fresh()->paymentIntents->first()->intent_id;
    $data['data']['object']['id'] = $paymentIntentId;

    $response = $this
        ->post(
            '/stripe/webhook',
            $data,
            ['Stripe-Signature' => $this->determineStripeSignature($data)],
        );

    $response->assertSuccessful();

    Bus::assertDispatched(HandleOtherEvent::class);
})->group('webhooks');

it('does not handle events with incorrect eshop_id if configured', function () {
    /** @var TestCase $this */
    Bus::fake([HandlePaymentIntentSucceeded::class]);

    Config::set('dystore.stripe.handle_eshop_ids', ['Fake Store']);

    $data = json_decode(file_get_contents(__DIR__.'/../Stubs/Stripe/payment_intent.succeeded.json'), true);

    $response = $this
        ->post(
            '/stripe/webhook',
            $data,
            ['Stripe-Signature' => $this->determineStripeSignature($data)],
        );

    $response->assertSuccessful();

    Bus::assertNotDispatched(HandlePaymentIntentSucceeded::class);
})->group('webhooks');
