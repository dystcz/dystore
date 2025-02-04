<?php

namespace Dystore\Api\Domain\Carts\Http\Controllers;

use Dystore\Api\Base\Controller;
use Dystore\Api\Domain\Carts\Actions\CreateUserFromCart;
use Dystore\Api\Domain\Carts\Contracts\CheckoutCart;
use Dystore\Api\Domain\Carts\Contracts\CheckoutCartController as CheckoutCartControllerContract;
use Dystore\Api\Domain\Carts\Contracts\CurrentSessionCart;
use Dystore\Api\Domain\Carts\JsonApi\V1\CheckoutCartRequest;
use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Domain\Orders\Models\Order;
use Illuminate\Validation\ValidationException;
use LaravelJsonApi\Contracts\Store\Store as StoreContract;
use LaravelJsonApi\Core\Responses\DataResponse;
use Lunar\Exceptions\Carts\CartException;

class CheckoutCartController extends Controller implements CheckoutCartControllerContract
{
    /**
     * Checkout user's cart.
     */
    public function checkout(
        StoreContract $store,
        CheckoutCartRequest $request,
        CreateUserFromCart $createUserFromCartAction,
        CheckoutCart $checkoutCartAction,
        ?CurrentSessionCart $cart
    ): DataResponse {
        /** @var Cart $cart */
        $this->authorize('checkout', $cart);

        if ($request->validated('create_user', false)) {
            $createUserFromCartAction($cart);
        }

        try {
            /** @var Order $order */
            $order = ($checkoutCartAction)($cart);
        } catch (CartException $e) {
            throw ValidationException::withMessages($e->errors()->getMessages());
        }

        return DataResponse::make($order)
            ->withIncludePaths([
                'product_lines',
                'product_lines.purchasable',
            ])
            ->didCreate();
    }
}
