<?php

/*
 * Lunar API Reviews Configuration
 */
return [
    // Configuration for specific domains
    'domains' => [
        'reviews' => [
            'model' => Dystcz\LunarApiReviews\Domain\Reviews\Models\Review::class,
            'lunar_model' => null,
            'policy' => Dystcz\LunarApiReviews\Domain\Reviews\Policies\ReviewPolicy::class,
            'schema' => Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewSchema::class,
            'resource' => Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewResource::class,
            'query' => Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewQuery::class,
            'collection_query' => Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewCollectionQuery::class,
            'routes' => Dystcz\LunarApiReviews\Domain\Reviews\Http\Routing\ReviewRouteGroup::class,
            'route_actions' => [
                // 'publish' => Dystcz\LunarApiReviews\Domain\Reviews\Http\Controllers\PublishReviewsController::class,
                // 'unpublish' => Dystcz\LunarApiReviews\Domain\Reviews\Http\Controllers\PublishReviewsController::class,
            ],
            'settings' => [
                'auth_middleware' => ['auth'],
            ],
        ],
    ],
];
