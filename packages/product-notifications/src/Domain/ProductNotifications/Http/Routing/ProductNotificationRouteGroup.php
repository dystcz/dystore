<?php

namespace Dystore\ProductNotifications\Domain\ProductNotifications\Http\Routing;

use Dystcz\LunarApi\Routing\RouteGroup;
use Dystore\ProductNotifications\Domain\ProductNotifications\Http\Controllers\ProductNotificationsController;
use Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;

class ProductNotificationRouteGroup extends RouteGroup
{
    /**
     * Register routes.
     */
    public function routes(?string $prefix = null, array|string $middleware = []): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function ($server) {
                $server->resource(ProductNotificationSchema::type(), ProductNotificationsController::class)
                    ->only('store');
            });
    }
}
