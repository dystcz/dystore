<?php

namespace Dystcz\LunarApi\Domain\Products\Http\Routing;

use Dystcz\LunarApi\Domain\Products\Contracts\ProductsController;
use Dystcz\LunarApi\Routing\Contracts\RouteGroup as RouteGroupContract;
use Dystcz\LunarApi\Routing\RouteGroup;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\Relationships;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

class ProductRouteGroup extends RouteGroup implements RouteGroupContract
{
    /**
     * Register routes.
     */
    public function routes(): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function (ResourceRegistrar $server) {
                $server->resource($this->getPrefix(), ProductsController::class)
                    ->relationships(function (Relationships $relationships) {
                        $relationships->hasMany('product_associations')->readOnly();
                        $relationships->hasOne('brand')->readOnly();
                        $relationships->hasOne('cheapest_product_variant')->readOnly();
                        $relationships->hasOne('channels')->readOnly();
                        $relationships->hasOne('most_expensive_product_variant')->readOnly();
                        $relationships->hasMany('collections')->readOnly();
                        $relationships->hasOne('default_url')->readOnly();
                        $relationships->hasMany('images')->readOnly();
                        $relationships->hasMany('inverse_product_associations')->readOnly();
                        $relationships->hasOne('lowest_price')->readOnly();
                        $relationships->hasOne('highest_price')->readOnly();
                        $relationships->hasMany('prices')->readOnly();
                        $relationships->hasMany('tags')->readOnly();
                        $relationships->hasOne('thumbnail')->readOnly();
                        $relationships->hasMany('urls')->readOnly();
                        $relationships->hasMany('product_variants')->readOnly();
                        $relationships->hasMany('product_options')->readOnly();
                    })
                    ->only('index', 'show')
                    ->readOnly();
            });
    }
}
