<?php

namespace Dystcz\LunarApiStripeAdapter;

use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentAdapter;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentIntent;
use Dystcz\LunarApiStripeAdapter\Jobs\HandleWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Lunar\Models\Cart;
use Lunar\Stripe\Facades\StripeFacade;

class StripePaymentAdapter extends PaymentAdapter
{
    /**
     * Get payment driver.
     */
    public function getDriver(): string
    {
        return Config::get('lunar-api.stripe.driver', 'stripe');
    }

    /**
     * Get payment type.
     */
    public function getType(): string
    {
        return Config::get('lunar-api.stripe.type', 'stripe');
    }

    /**
     * Create payment intent.
     */
    public function createIntent(Cart $cart, array $meta = []): PaymentIntent
    {
        /** @var Stripe\PaymentIntent $stripePaymentIntent */
        $stripePaymentIntent = StripeFacade::createIntent($cart->calculate());

        $paymentIntent = new PaymentIntent(
            id: $stripePaymentIntent->id,
            amount: $stripePaymentIntent->amount,
            status: 'intent',
            client_secret: $stripePaymentIntent->client_secret,
        );

        $this->createTransaction($cart, $paymentIntent);

        return $paymentIntent;
    }

    /**
     * Handle incoming Stripe webhook.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        HandleWebhook::dispatch(
            data: $request->all(),
            payload: $request->getContent(),
            signature: $request->header('Stripe-Signature'),
        );

        return new JsonResponse(null, 200);
    }
}
