<?php

namespace Dystcz\LunarApi\Domain\Carts\Http\Controllers;

use Dystcz\LunarApi\Base\Controller;
use Dystcz\LunarApi\Domain\CartAddresses\JsonApi\V1\CartAddressSchema;
use Dystcz\LunarApi\Domain\Carts\Contracts\CartShippingOptionController as CartShippingOptionControllerContract;
use Dystcz\LunarApi\Domain\Carts\Contracts\CurrentSessionCart;
use Dystcz\LunarApi\Domain\Carts\JsonApi\V1\SetShippingOptionRequest;
use Dystcz\LunarApi\Domain\Carts\JsonApi\V1\UnsetShippingOptionRequest;
use Dystcz\LunarApi\Domain\Carts\Models\Cart;
use LaravelJsonApi\Core\Responses\DataResponse;

class CartShippingOptionController extends Controller implements CartShippingOptionControllerContract
{
    /**
     * Set shipping option to cart.
     */
    public function setShippingOption(
        CartAddressSchema $schema,
        SetShippingOptionRequest $request,
        ?CurrentSessionCart $cart,
    ): DataResponse {
        /** @var Cart $cart */
        $this->authorize('updateShippingOption', $cart);

        $cartAddress = $cart
            ->addresses()
            ->where('type', $request->input('data.attributes.address_type'))
            ->firstOrFail();

        // Set shipping option
        $cartAddress->update([
            'shipping_option' => $request->input('data.attributes.shipping_option'),
        ]);

        $model = $schema
            ->repository()
            ->queryOne($cartAddress)
            ->withRequest($request)
            ->first();

        return DataResponse::make($model)
            ->didntCreate();
    }

    /**
     * Unset shipping option from cart.
     */
    public function unsetShippingOption(
        CartAddressSchema $schema,
        UnsetShippingOptionRequest $request,
        ?CurrentSessionCart $cart,
    ): DataResponse {
        /** @var Cart $cart */
        $this->authorize('updateShippingOption', $cart);

        $cartAddress = $cart
            ->addresses()
            ->where('type', $request->input('data.attributes.address_type'))
            ->firstOrFail();

        // Unset shipping option
        $cartAddress->update([
            'shipping_option' => null,
        ]);

        $model = $schema
            ->repository()
            ->queryOne($cartAddress)
            ->withRequest($request)
            ->first();

        return DataResponse::make($model)
            ->didntCreate();
    }
}
