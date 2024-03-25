<?php

namespace Dystcz\LunarApi\Domain\PaymentOptions\Pipelines;

use Closure;
use Dystcz\LunarApi\Domain\Carts\ValueObjects\PaymentBreakdown;
use Dystcz\LunarApi\Domain\Carts\ValueObjects\PaymentBreakdownItem;
use Dystcz\LunarApi\Domain\PaymentOptions\Facades\PaymentManifest;
use Lunar\DataTypes\Price;
use Lunar\Models\Cart;

class ApplyPayment
{
    /**
     * Called just before cart totals are calculated.
     *
     * @param  Closure(Cart): void  $next
     */
    public function handle(Cart $cart, Closure $next): void
    {
        $paymentSubTotal = 0;
        $paymentBreakdown = $cart->paymentBreakdown ?: new PaymentBreakdown;

        $paymentOption = $cart->paymentOptionOverride ?: PaymentManifest::getPaymentOption($cart);

        if ($paymentOption) {
            $paymentBreakdown->items->put(
                $paymentOption->getIdentifier(),
                new PaymentBreakdownItem(
                    name: $paymentOption->getName(),
                    identifier: $paymentOption->getIdentifier(),
                    price: $paymentOption->price,
                )
            );

            $paymentSubTotal = $paymentOption->price->value;
            $paymentTotal = $paymentSubTotal;
        }

        $cart->paymentBreakdown = $paymentBreakdown;

        $cart->paymentSubTotal = new Price(
            $paymentBreakdown->items->sum('price.value'),
            $cart->currency,
            1
        );

        $next($cart);
    }
}