<?php

namespace Dystcz\LunarApiStripeAdapter\Jobs\Webhooks;

use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentCanceled;

class HandlePaymentIntentCancelled extends WebhookHandler
{
    /**
     * Handle failed payment intent.
     */
    public function handle(): void
    {
        $event = $this->constructStripeEvent();
        $paymentIntent = $this->getPaymentIntentFromEvent($event);
        $order = $this->findOrderByIntent($paymentIntent);
        $paymentAdapter = $this->getPaymentAdapter();

        OrderPaymentCanceled::dispatch($order, $paymentAdapter, $paymentIntent);
    }
}
