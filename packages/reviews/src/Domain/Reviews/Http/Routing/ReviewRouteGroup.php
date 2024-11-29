<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\Http\Routing;

use Dystcz\LunarApi\Domain\Products\Http\Controllers\ProductsController;
use Dystcz\LunarApi\Domain\Products\Http\Routing\ProductRouteGroup;
use Dystcz\LunarApi\Domain\Products\JsonApi\V1\ProductSchema;
use Dystcz\LunarApi\Domain\ProductVariants\JsonApi\V1\ProductVariantSchema;
use Dystcz\LunarApi\Routing\RouteGroup;
use Dystcz\LunarApiReviews\Domain\ProductVariants\Http\Controllers\ProductVariantsController;
use Dystcz\LunarApiReviews\Domain\Reviews\Http\Controllers\PublishReviewsController;
use Dystcz\LunarApiReviews\Domain\Reviews\Http\Controllers\ReviewsController;
use Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewSchema;
use Illuminate\Support\Facades\App;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;

class ReviewRouteGroup extends RouteGroup
{
    public array $middleware = [];

    /**
     * Register routes.
     */
    public function routes(?string $prefix = null, array|string $middleware = []): void
    {
        
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function ($server) {
                $server->resource(ReviewSchema::type(), ReviewsController::class)
                    ->except('update');

                $server->resource(ReviewSchema::type(), PublishReviewsController::class)
                    ->only('')
                    ->actions('-actions', function ($actions) {
                        $actions->withId()->post('publish');
                        $actions->withId()->delete('unpublish');
                    });
                
                $server->resource(ProductSchema::type(), ProductsController::class)
                    ->relationships(function ($relationships) {
                        $relationships->hasMany('reviews')->only('index')->readOnly();
                    })->only();

                $server->resource(ProductVariantSchema::type(), ProductVariantsController::class)
                    ->relationships(function ($relationships) {
                        $relationships->hasMany('reviews')->only('index')->readOnly();
                    })->only();
            });
    }
}
