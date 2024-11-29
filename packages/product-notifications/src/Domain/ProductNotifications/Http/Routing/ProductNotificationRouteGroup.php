<?php

namespace Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Http\Routing;

use Dystcz\LunarApi\Routing\RouteGroup;
use Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Http\Controllers\ProductNotificationsController;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;

class ProductNotificationRouteGroup extends RouteGroup
{
    public string $prefix = 'product-notifications';

    public array $middleware = [];

    /**
     * Register routes.
     */
    public function routes(?string $prefix = null, array|string $middleware = []): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function ($server) {
                $server->resource($this->getPrefix(), ProductNotificationsController::class)
                    ->only('store');
            });
    }
}
