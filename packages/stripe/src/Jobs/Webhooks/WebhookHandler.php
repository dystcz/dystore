<?php

namespace Dystcz\LunarApiStripeAdapter\Jobs\Webhooks;

use Dystcz\LunarApi\Domain\Orders\Actions\FindOrderByCartIntent;
use Dystcz\LunarApi\Domain\Orders\Actions\FindOrderByIntent;
use Dystcz\LunarApi\Domain\Orders\Actions\FindOrderByTransaction;
use Dystcz\LunarApi\Domain\Payments\Contracts\PaymentIntent as PaymentIntentContract;
use Dystcz\LunarApi\Domain\Payments\Data\PaymentIntent;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentAdapter;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentAdaptersRegister;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Lunar\Models\Order;
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

        return $register->get(Config::get('lunar-api.stripe.driver', 'stripe'));
    }

    /**
     * Find order by payment intent.
     *
     * @throws ModelNotFoundException
     */
    protected function findOrder(PaymentIntentContract $paymentIntent): Order
    {
        try {
            $order = App::make(FindOrderByIntent::class)($paymentIntent)
                ?? App::make(FindOrderByTransaction::class)($paymentIntent)
                ?? App::make(FindOrderByCartIntent::class)($paymentIntent);
        } catch (Throwable $e) {
            $this->fail($e);
        }

        return $order;
    }
}
