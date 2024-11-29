<?php

namespace Dystcz\LunarApiStripeAdapter\Jobs\Webhooks;

class HandlePaymentIntentRequiresAction extends WebhookHandler
{
    /**
     * Handle payment intent processing.
     */
    public function handle(): void
    {
        // $event = $this->constructStripeEvent();
        // $paymentIntent = $this->getPaymentIntentFromEvent($event);
        // $order = $this->findOrderByIntent($paymentIntent);
        // $paymentAdapter = $this->getPaymentAdapter();
    }
}
