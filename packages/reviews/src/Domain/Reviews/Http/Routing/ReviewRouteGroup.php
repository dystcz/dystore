<?php

namespace Dystore\Reviews\Domain\Reviews\Http\Routing;

use Dystore\Api\Domain\Orders\Contracts\OrdersController;
use Dystore\Api\Domain\Orders\JsonApi\V1\OrderSchema;
use Dystore\Api\Domain\Products\Contracts\ProductsController;
use Dystore\Api\Domain\Products\JsonApi\V1\ProductSchema;
use Dystore\Api\Domain\ProductVariants\Contracts\ProductVariantsController;
use Dystore\Api\Domain\ProductVariants\JsonApi\V1\ProductVariantSchema;
use Dystore\Api\Routing\RouteGroup;
use Dystore\Reviews\Domain\Reviews\Contacts\PublishReviewsController;
use Dystore\Reviews\Domain\Reviews\Contacts\ReviewsController;
use Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewSchema;
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
                    ->relationships(function (Relationships $relationships) {
                        $relationships->hasMany('images')->readOnly();
                    })
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
                        $relationships->hasMany('reviews')->readOnly();
                        $relationships->hasMany('product_variant_reviews')->readOnly();
                    })->only();

                $server
                    ->resource(ProductVariantSchema::type(), ProductVariantsController::class)
                    ->relationships(function (Relationships $relationships) {
                        $relationships->hasMany('reviews')->readOnly();
                    })->only();

                $server
                    ->resource(OrderSchema::type(), OrdersController::class)
                    ->relationships(function (Relationships $relationships) {
                        $relationships->hasMany('reviews')->readOnly();
                    })->only();
            });
    }
}
