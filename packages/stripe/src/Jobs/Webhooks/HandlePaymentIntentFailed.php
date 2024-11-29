<?php

namespace Dystcz\LunarApiStripeAdapter\Jobs\Webhooks;

use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentFailed;

class HandlePaymentIntentFailed extends WebhookHandler
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

        OrderPaymentFailed::dispatch($order, $paymentAdapter, $paymentIntent);
    }
}
