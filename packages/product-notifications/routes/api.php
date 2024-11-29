<?php

use Dystcz\LunarProductNotification\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => Config::get('lunar-product-notifications.route_prefix'),
    'middleware' => Config::get('lunar-product-notifications.route_middleware'),
], function (Router $router) {
    foreach (Arr::flatten(Arr::pluck(Config::get('lunar-product-notifications.domains'), 'route_groups')) as $key => $group) {
        $group::make(ProductNotificationSchema::type())->routes();
    }
});
