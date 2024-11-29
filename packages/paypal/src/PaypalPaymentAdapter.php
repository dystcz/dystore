<?php

namespace Dystcz\LunarApiPaypalAdapter;

use Dystcz\LunarApi\Domain\Orders\Actions\FindOrderByIntent;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentAdapter;
use Dystcz\LunarApi\Domain\Payments\PaymentAdapters\PaymentIntent;
use Dystcz\LunarApiPaypalAdapter\Actions\AuthorizePaypalPayment;
use Dystcz\LunarPaypal\Actions\VerifyWebhookSignature;
use Dystcz\LunarPaypal\Data\Order as PaypalOrder;
use Dystcz\LunarPaypal\Exceptions\InvalidWebhookSignatureException;
use Dystcz\LunarPaypal\Facades\PaypalFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Lunar\Models\Cart;

class PaypalPaymentAdapter extends PaymentAdapter
{
    public function getDriver(): string
    {
        return Config::get('lunar-api-paypal-adapter.driver');
    }

    public function getType(): string
    {
        return Config::get('lunar-api-paypal-adapter.type');
    }

    public function createIntent(Cart $cart): PaymentIntent
    {
        $this->cart = $cart;

        /** @var PaypalOrder $intent */
        $intent = PaypalFacade::createIntent($cart->calculate());

        $this->createTransaction($intent->id, $intent->totalAmount());

        return new PaymentIntent(
            id: $intent->id
        );
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        if ($request->event_type !== 'CHECKOUT.ORDER.APPROVED') {
            return response()->json();
        }

        try {
            if (Config::get('lunar.paypal.mode') !== 'sandbox') {
                App::make(VerifyWebhookSignature::class)(
                    body: $request->all(),
                    headers: $request->headers->all()
                );
            }
        } catch (InvalidWebhookSignatureException $e) {
            report($e);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $order = App::make(FindOrderByIntent::class)(
            $request->get('resource')['id']
        );

        if (! $order) {
            report('Order not found for payment intent: '.$request->get('resource')['id']);

            return response()->json(['error' => 'Order not found'], 404);
        }

        App::make(AuthorizePaypalPayment::class)($order);

        return response()->json(['message' => 'success']);
    }
}
