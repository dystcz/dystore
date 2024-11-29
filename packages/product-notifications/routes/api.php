<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => Config::get('lunar-api-product-notifications.route_prefix'),
    'middleware' => Config::get('lunar-api-product-notifications.route_middleware'),
], function (Router $router) {
    $domains = Collection::make(Config::get('lunar-api.domains'));

    foreach ($domains as $domain) {
        if (isset($domain['route_groups'])) {
            foreach ($domain['route_groups'] as $group) {
                /** @var RouteGroup $group */
                $group::make($domain['schema']::type())->routes();
            }
        }
    }
});
