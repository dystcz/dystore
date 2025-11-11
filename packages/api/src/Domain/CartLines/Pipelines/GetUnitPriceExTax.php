<?php

namespace Dystore\Api\Domain\CartLines\Pipelines;

use Closure;
use Lunar\DataTypes\Price;
use Lunar\Facades\Pricing;
use Lunar\Models\CartLine;
use Lunar\Models\Contracts\CartLine as CartLineContract;
use Spatie\LaravelBlink\BlinkFacade as Blink;

class GetUnitPriceExTax
{
    /**
     * Called just before cart totals are calculated.
     *
     * @param  Closure(CartLineContract): mixed  $next
     * @return Closure
     */
    public function handle(CartLineContract $cartLine, Closure $next)
    {
        /** @var CartLine $cart */
        $purchasable = $cartLine->purchasable;
        $cart = $cartLine->cart;

        if ($customer = $cart->customer) {
            $customerGroups = $customer->customerGroups;
        } else {
            $customerGroups = $cart->user?->customers->pluck('customerGroups')->flatten();
        }

        $currency = Blink::once('currency_'.$cart->currency_id, function () use ($cart) {
            return $cart->currency;
        });

        $priceResponse = Pricing::currency($currency)
            ->qty($cartLine->quantity)
            ->currency($cart->currency)
            ->customerGroups($customerGroups)
            ->for($purchasable)
            ->get();

        $cartLine->unitPriceExTax = new Price(
            $priceResponse->matched->priceExTax()->value,
            $cart->currency,
            $purchasable->getUnitQuantity()
        );

        $cartLine->unitTax = new Price(
            $cartLine->unitPriceInclTax->value - $cartLine->unitPriceExTax->value,
            $cart->currency,
            $purchasable->getUnitQuantity()
        );

        return $next($cartLine);
    }
}
