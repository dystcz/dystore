<?php

namespace Dystcz\LunarApiStripeAdapter\Actions;

use Dystcz\LunarApi\Domain\Orders\Events\OrderPaid;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentIntent;
use Illuminate\Support\Facades\Log;
use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\Models\Order;

class AuthorizeStripePayment
{
    public function __invoke(Order $order, Cart $cart, PaymentIntent $intent): void
    {
        Log::info('Payment intent succeeded: '.$intent->id);

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

        OrderPaid::dispatch($order);
    }
}
