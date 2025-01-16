<?php

namespace Dystore\Stripe\Actions;

use Dystore\Api\Domain\Orders\Events\OrderPaymentSuccessful;
use Dystore\Api\Domain\Payments\Contracts\PaymentIntent;
use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Facades\Payments;
use Lunar\Models\Contracts\Cart as CartContract;
use Lunar\Models\Contracts\Order as OrderContract;
use Lunar\Stripe\StripePaymentType;

class AuthorizeStripePayment
{
    public function __invoke(?OrderContract $order, ?CartContract $cart, PaymentIntent $intent): void
    {
        if (! $order && ! $cart) {
            throw new \InvalidArgumentException('Either order or cart must be provided');
        }

        /** @var StripePaymentType $driver */
        $driver = Payments::driver('stripe');

        if ($cart) {
            $driver->cart($cart);
        }

        if ($order) {
            $driver->order($order);
        }

        /** @var PaymentAuthorize $payment */
        $driver
            ->withData([
                'payment_intent_client_secret' => $intent->getClientSecret(),
                'payment_intent' => $intent->getId(),
            ]);

        /** @var PaymentAuthorize $authorization */
        $authorization = $driver->authorize();

        if (! $authorization->success) {
            report("Payment failed for order: {$order->id} with reason: {$authorization->message}");

            return;
        }

        OrderPaymentSuccessful::dispatch($order);
    }
}
