<?php

namespace Dystore\Api\Domain\Orders\Http\Routing;

use Dystore\Api\Domain\Orders\Contracts\CheckOrderPaymentStatusController;
use Dystore\Api\Domain\Orders\Contracts\CreatePaymentIntentController;
use Dystore\Api\Domain\Orders\Contracts\MarkOrderAwaitingPaymentController;
use Dystore\Api\Domain\Orders\Contracts\MarkOrderPendingPaymentController;
use Dystore\Api\Domain\Orders\Contracts\OrdersController;
use Dystore\Api\Routing\RouteGroup;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\ActionRegistrar;
use LaravelJsonApi\Laravel\Routing\Relationships;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

class OrderRouteGroup extends RouteGroup
{
    /**
     * Register routes.
     */
    public function routes(): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function (ResourceRegistrar $server) {
                $server->resource($this->getPrefix(), OrdersController::class)
                    ->relationships(function (Relationships $relationships) {
                        $relationships->hasMany('order_lines')->readOnly();
                        $relationships->hasMany('product_lines')->readOnly();
                        $relationships->hasMany('digital_lines')->readOnly();
                        $relationships->hasMany('physical_lines')->readOnly();
                        $relationships->hasMany('shipping_lines')->readOnly();
                        $relationships->hasMany('payment_lines')->readOnly();
                        $relationships->hasMany('transactions')->readOnly();
                    })
                    ->only('show', 'update');

                $server->resource($this->getPrefix(), CreatePaymentIntentController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->withId()->post('create-payment-intent');
                    });

                $server->resource($this->getPrefix(), MarkOrderPendingPaymentController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->withId()->patch('mark-pending-payment');
                    });

                $server->resource($this->getPrefix(), MarkOrderAwaitingPaymentController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->withId()->patch('mark-awaiting-payment');
                    });

                $server->resource($this->getPrefix(), CheckOrderPaymentStatusController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->withId()->get('check-order-payment-status');
                    });
            });
    }
}
