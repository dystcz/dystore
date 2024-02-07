<?php

namespace Dystcz\LunarApiStripeAdapter\Actions;

use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentSuccessful;
use Dystcz\LunarApi\Domain\Payments\Contracts\PaymentIntent;
use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\Models\Order;

class AuthorizeStripePayment
{
    public function __invoke(Order $order, Cart $cart, PaymentIntent $intent): void
    {
        /** @var PaymentAuthorize $payment */
        $payment = Payments::driver('stripe')
            ->order($order)
            ->cart($cart)
            ->withData([
                'payment_intent_client_secret' => $intent->client_secret,
                'payment_intent' => $intent->id,
            ])
            ->authorize();

        if (! $payment->success) {
            report("Payment failed for order: {$order->id} with reason: $payment->message");

            return;
        }

        OrderPaymentSuccessful::dispatch($order);
    }
}
