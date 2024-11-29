<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\Http\Routing;

use Dystcz\LunarApi\Domain\Products\Http\Controllers\ProductsController;
use Dystcz\LunarApi\Domain\Products\JsonApi\V1\ProductSchema;
use Dystcz\LunarApi\Domain\ProductVariants\Http\Controllers\ProductVariantsController;
use Dystcz\LunarApi\Domain\ProductVariants\JsonApi\V1\ProductVariantSchema;
use Dystcz\LunarApi\Routing\RouteGroup;
use Dystcz\LunarApiReviews\Domain\Reviews\Http\Controllers\PublishReviewsController;
use Dystcz\LunarApiReviews\Domain\Reviews\Http\Controllers\ReviewsController;
use Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewSchema;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\Relationships;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

class ReviewRouteGroup extends RouteGroup
{
    /**
     * Register routes.
     */
    public function routes(): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function (ResourceRegistrar $server) {
                $server
                    ->resource(ReviewSchema::type(), ReviewsController::class)
                    ->only('index', 'show', 'store');

                $server
                    ->resource(ReviewSchema::type(), PublishReviewsController::class)
                    ->actions('-actions', function ($actions) {
                        $actions->withId()->post('publish');
                        $actions->withId()->delete('unpublish');
                    })->only();

                $server
                    ->resource(ProductSchema::type(), ProductsController::class)
                    ->relationships(function (Relationships $relationships) {
                        $relationships->hasMany('reviews')
                            ->readOnly();
                    })->only();

                $server
                    ->resource(ProductVariantSchema::type(), ProductVariantsController::class)
                    ->relationships(function (Relationships $relationships) {
                        $relationships->hasMany('reviews')
                            ->readOnly();
                    })->only();
            });
    }
}
