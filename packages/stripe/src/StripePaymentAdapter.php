<?php

namespace Dystcz\LunarApiStripeAdapter;

use Dystcz\LunarApi\Domain\Orders\Actions\FindOrderByIntent;
use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentCanceled;
use Dystcz\LunarApi\Domain\Orders\Events\OrderPaymentFailed;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentAdapter;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentIntent;
use Dystcz\LunarApiStripeAdapter\Actions\AuthorizeStripePayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Cart;
use Lunar\Stripe\Facades\StripeFacade;
use RuntimeException;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripePaymentAdapter extends PaymentAdapter
{
    public function getDriver(): string
    {
        return Config::get('lunar-api-stripe-adapter.driver');
    }

    public function getType(): string
    {
        return Config::get('lunar-api-stripe-adapter.type');
    }

    public function createIntent(Cart $cart): PaymentIntent
    {
        $this->cart = $cart;

        /** @var \Stripe\PaymentIntent $intent */
        $intent = StripeFacade::createIntent($cart->calculate());

        $this->createTransaction($intent->id, $intent->amount);

        return new PaymentIntent(
            id: $intent->id
        );
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        if (config('app.env') !== 'testing') {
            $event = $this->constructEventForNonTestingEnv($request);

            if ($event instanceof JsonResponse) {
                return $event;
            }
        } else {
            $event = Event::constructFrom($request->all());
        }

        $paymentIntent = $event->data->object;
        $order = App::make(FindOrderByIntent::class)($paymentIntent->id);

        if (! $order) {
            throw new RuntimeException('Order not found for payment intent: '.$paymentIntent->id);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                App::make(AuthorizeStripePayment::class)($order, $paymentIntent);
                break;
            case 'payment_intent.canceled':
                OrderPaymentCanceled::dispatch($order, $paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                OrderPaymentFailed::dispatch($order, $paymentIntent);
                break;
            default:
                Log::info('Received unknown event type '.$event->type);
        }

        return response()->json(['message' => 'success']);
    }

    protected function constructEventForNonTestingEnv(Request $request): JsonResponse|Event
    {
        $payload = file_get_contents('php://input');

        try {
            return Webhook::constructEvent(
                $payload,
                $request->header('Stripe-Signature'),
                Config::get('services.stripe.webhook_secret')
            );
        } catch (UnexpectedValueException $e) {
            report($e);

            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            report($e);

            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }
}
