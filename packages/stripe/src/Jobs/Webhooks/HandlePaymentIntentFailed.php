<?php

namespace Dystore\Stripe\Jobs\Webhooks;

use Dystore\Api\Domain\Orders\Events\OrderPaymentFailed;

class HandlePaymentIntentFailed extends WebhookHandler
{
    /**
     * Handle failed payment intent.
     */
    public function handle(): void
    {
        $event = $this->constructStripeEvent();
        $paymentIntent = $this->getPaymentIntentFromEvent($event);
        $order = $this->findOrder($paymentIntent);
        $paymentAdapter = $this->getPaymentAdapter();

        OrderPaymentFailed::dispatch($order, $paymentAdapter, $paymentIntent);
    }
}
