<?php

namespace Dystore\Api\Domain\CartLines\Actions;

use Dystore\Api\Domain\CartLines\Data\CartLineData;
use Dystore\Api\Domain\Carts\Contracts\CurrentSessionCart;
use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Support\Actions\Action;
use Illuminate\Support\Facades\App;
use Lunar\Models\Contracts\CartLine as CartLineContract;

class UpdateCartLine extends Action
{
    public function handle(CartLineData $data, CartLineContract $cartLine): array
    {
        /** @var Cart $cart */
        $cart = App::make(CurrentSessionCart::class);

        $cart = $cart->updateLine(
            cartLineId: $cartLine->id,
            quantity: $data->quantity,
            meta: $data->meta ?? []
        );

        $cartLine = $cart->lines->firstWhere(
            fn (CartLineContract $cartLine) => $cartLine->is($cartLine)
        );

        return [$cart, $cartLine];
    }
}
