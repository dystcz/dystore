<?php

namespace Dystore\Stripe\Jobs\Webhooks;

use Dystore\Api\Domain\Orders\Actions\FindOrderByCartIntent;
use Dystore\Api\Domain\Orders\Actions\FindOrderByIntent;
use Dystore\Api\Domain\Orders\Actions\FindOrderByTransaction;
use Dystore\Api\Domain\Payments\Contracts\PaymentIntent as PaymentIntentContract;
use Dystore\Api\Domain\Payments\Data\PaymentIntent;
use Dystore\Api\Domain\Payments\PaymentAdapters\PaymentAdapter;
use Dystore\Api\Domain\Payments\PaymentAdapters\PaymentAdaptersRegister;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Lunar\Models\Contracts\Order;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Event;
use Throwable;

abstract class WebhookHandler implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public WebhookCall $webhookCall;

    public function __construct(
        WebhookCall $webhookCall,
    ) {
        $this->webhookCall = $webhookCall;
    }

    /**
     * Handle the webhook.
     */
    abstract public function handle(): void;

    /**
     * Construct Stripe event.
     */
    protected function constructStripeEvent(): Event
    {
        try {
            $event = Event::constructFrom($this->webhookCall->payload);
        } catch (Throwable $e) {
            $this->fail($e);
        }

        return $event;
    }

    /**
     * Get payment intent from event.
     */
    protected function getPaymentIntentFromEvent(Event $event): PaymentIntentContract
    {
        return new PaymentIntent(intent: $event->data->object);
    }

    /**
     * Get payment adapter.
     */
    protected function getPaymentAdapter(): PaymentAdapter
    {
        $register = App::make(PaymentAdaptersRegister::class);

        return $register->get(Config::get('dystore.stripe.driver', 'stripe'));
    }

    /**
     * Find order by payment intent.
     *
     * @throws ModelNotFoundException
     */
    protected function findOrder(PaymentIntentContract $paymentIntent): ?Order
    {
        try {
            $order = App::make(FindOrderByIntent::class)($paymentIntent);

            return $order;
        } catch (Throwable $e) {
            // $this->fail($e);
        }

        try {
            $order = App::make(FindOrderByTransaction::class)($paymentIntent);

            return $order;
        } catch (Throwable $e) {
            // $this->fail($e);
        }

        try {
            $order = App::make(FindOrderByCartIntent::class)($paymentIntent);

            return $order;
        } catch (Throwable $e) {
            // $this->fail($e);
        }

        $this->fail(new ModelNotFoundException('Order not found.'));

        return null;
    }
}
