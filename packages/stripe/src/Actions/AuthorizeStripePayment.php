<?php

namespace Dystcz\LunarApiStripeAdapter\Actions;

use Dystcz\LunarApi\Domain\Orders\Events\OrderPaid;
use Dystcz\LunarApi\Domain\Orders\Models\Order;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentIntent;
use Illuminate\Support\Facades\Log;
use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Facades\Payments;

class AuthorizeStripePayment
{
    public function __invoke(Order $order, PaymentIntent $intent): void
    {
        Log::info('Payment intent succeeded: '.$intent->id);

        /** @var PaymentAuthorize $payment */
        $payment = Payments::driver('stripe')
            ->order($order)
            ->cart($order->cart)
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
