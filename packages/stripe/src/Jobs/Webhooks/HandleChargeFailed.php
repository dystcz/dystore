<?php

namespace Dystore\Stripe\Jobs\Webhooks;

class HandleChargeFailed extends WebhookHandler
{
    /**
     * Handle payment intent processing.
     */
    public function handle(): void
    {
        // $event = $this->constructStripeEvent();
    }
}
