<?php

namespace Dystcz\LunarApiStripeAdapter\Jobs\Webhooks;

use Dystcz\LunarApiStripeAdapter\Actions\AuthorizeStripePayment;
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
