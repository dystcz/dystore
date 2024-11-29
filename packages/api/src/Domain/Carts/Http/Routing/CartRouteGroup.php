<?php

namespace Dystcz\LunarApi\Domain\Carts\Http\Routing;

use Dystcz\LunarApi\Domain\Carts\Contracts\CheckoutCartController;
use Dystcz\LunarApi\Domain\Carts\Http\Controllers\CartPaymentOptionController;
use Dystcz\LunarApi\Domain\Carts\Http\Controllers\CartsController;
use Dystcz\LunarApi\Domain\Carts\Http\Controllers\ClearUserCartController;
use Dystcz\LunarApi\Domain\Carts\Http\Controllers\CouponsController;
use Dystcz\LunarApi\Domain\Carts\Http\Controllers\CreateEmptyCartAddressesController;
use Dystcz\LunarApi\Domain\Carts\Http\Controllers\ReadUserCartController;
use Dystcz\LunarApi\Routing\RouteGroup;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\ActionRegistrar;
use LaravelJsonApi\Laravel\Routing\Relationships;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

class CartRouteGroup extends RouteGroup
{
    /**
     * Register routes.
     */
    public function routes(): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function (ResourceRegistrar $server) {
                $server->resource($this->getPrefix(), CartsController::class)
                    ->relationships(function (Relationships $relationships) {
                        $relationships->hasMany('cart_lines')->readOnly();
                    })
                    ->only('show');

                $server->resource($this->getPrefix(), ClearUserCartController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->delete('clear');
                    });

                $server->resource($this->getPrefix(), ReadUserCartController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->get('my-cart');
                    });

                $server->resource($this->getPrefix(), CreateEmptyCartAddressesController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->post('create-empty-addresses');
                    });

                $server->resource($this->getPrefix(), CheckoutCartController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->post('checkout');
                    });

                $server->resource($this->getPrefix(), CouponsController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->post('set-coupon');
                        $actions->post('unset-coupon');
                    });

                $server->resource($this->getPrefix(), CartPaymentOptionController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->post('set-payment-option');
                        $actions->post('unset-payment-option');
                    });
            });
    }
}
