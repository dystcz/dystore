<?php

namespace Dystore\Stripe\Jobs\Webhooks;

use Dystore\Stripe\Actions\AuthorizeStripePayment;
use Illuminate\Support\Facades\App;

class HandlePaymentIntentSucceeded extends WebhookHandler
{
    /**
     * Handle failed payment intent.
     */
    public function handle(): void
    {
        $event = $this->constructStripeEvent();
        $paymentIntent = $this->getPaymentIntentFromEvent($event);
        $order = $this->findOrder($paymentIntent);

        App::make(AuthorizeStripePayment::class)($order, $order->cart, $paymentIntent);
    }
}
