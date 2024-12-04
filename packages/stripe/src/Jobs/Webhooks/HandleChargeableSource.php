<?php

namespace Dystore\Stripe\Jobs\Webhooks;

class HandleChargeableSource extends WebhookHandler
{
    /**
     * Handle payment intent processing.
     */
    public function handle(): void
    {
        // $event = $this->constructStripeEvent();
    }
}
