<?php

namespace Dystcz\LunarApiStripeAdapter\Jobs;

use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentCanceled;
use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentFailed;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentIntent;
use Dystcz\LunarApiStripeAdapter\Actions\AuthorizeStripePayment;
use Dystcz\LunarApiStripeAdapter\StripePaymentAdapter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Cart;
use Stripe\Event;
use Stripe\Webhook;

class HandleWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public StripePaymentAdapter $adapter;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $data,
        public string $payload,
        public string $signature,
    ) {
        $this->adapter = new StripePaymentAdapter;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $event = $this->constructEvent();

        $stripePaymentIntent = $event->data->object;

        $statusMap = Config::get('lunar-api.stripe.payment_intent_status_map', []);

        $paymentIntentStatus = match (true) {
            in_array($event->type, array_keys($statusMap)) => $statusMap[$event->type],
            default => 'unknown',
        };

        $paymentIntent = new PaymentIntent(
            id: $stripePaymentIntent->id,
            amount: $stripePaymentIntent->amount,
            status: $paymentIntentStatus,
            client_secret: $stripePaymentIntent->client_secret,
        );

        try {
            $cart = Cart::query()
                ->with(['draftOrder', 'completedOrder'])
                ->where('meta->payment_intent', $paymentIntent->id)
                ->firstOrFail();
        } catch (Exception $e) {
            $this->fail($e);
        }

        /** @var Order $order */
        $order = $cart->draftOrder ? $cart->draftOrder : $cart->completedOrder;

        switch ($paymentIntentStatus) {
            case 'succeeded':
                App::make(AuthorizeStripePayment::class)($order, $cart, $paymentIntent);
                break;
            case 'canceled':
                OrderPaymentCanceled::dispatch($order, $this->adapter, $paymentIntent);
                break;
            case 'failed':
                OrderPaymentFailed::dispatch($order, $this->adapter, $paymentIntent);
                break;
            default:
                Log::info('Received unknown event type '.$event->type);
        }
    }

    /**
     * Construct Stripe event.
     */
    protected function constructEvent(): Event
    {
        if (App::environment('testing')) {
            return Event::constructFrom($this->data);
        }

        try {
            return Webhook::constructEvent(
                $this->payload,
                $this->signature,
                Config::get('services.stripe.webhooks.payment_intent'),
            );
        } catch (Exception $e) {
            $this->fail($e);
        }
    }
}
