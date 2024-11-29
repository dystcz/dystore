<?php

namespace Dystcz\LunarApiStripeAdapter\Jobs\Webhooks;

use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentCanceled;

class HandlePaymentIntentCanceled extends WebhookHandler
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

        OrderPaymentCanceled::dispatch($order, $paymentAdapter, $paymentIntent);
    }
}
