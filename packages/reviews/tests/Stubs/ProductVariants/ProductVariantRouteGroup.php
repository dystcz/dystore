<?php

namespace Dystcz\LunarApiReviews\Tests\Stubs\ProductVariants;

use Dystcz\LunarApi\Routing\RouteGroup;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;

class ProductVariantRouteGroup extends RouteGroup
{
    public string $prefix = 'variants';

    public array $middleware = [];

    /**
     * Register routes.
     */
    public function routes(string $prefix = null, array|string $middleware = []): void
    {
        Route::group([
            'prefix' => Config::get('lunar-api.general.route_prefix'),
            'middleware' => Config::get('lunar-api.general.route_middleware'),
        ], function () {
            JsonApiRoute::server('v1')
                ->prefix('v1')
                ->resources(function ($server) {
                    $server->resource($this->getPrefix(), ProductVariantsController::class)
                        ->only('index', 'show')
                        ->relationships(function ($relations) {
                            $relations->hasMany('reviews');
                        });
                });
        });
    }
}
