<?php

use Dystcz\LunarApi\Support\Models\Actions\SchemaType;

/*
 * Lunar API Reviews Configuration
 */
return [
    // Configuration for specific domains
    'domains' => [
        SchemaType::get(Dystcz\LunarApiReviews\Domain\Reviews\Models\Review::class) => [
            'model' => Dystcz\LunarApiReviews\Domain\Reviews\Models\Review::class,
            'lunar_model' => null,
            'policy' => Dystcz\LunarApiReviews\Domain\Reviews\Policies\ReviewPolicy::class,
            'schema' => Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewSchema::class,
            'resource' => Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewResource::class,
            'query' => Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewQuery::class,
            'collection_query' => Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewCollectionQuery::class,
            'routes' => Dystcz\LunarApiReviews\Domain\Reviews\Http\Routing\ReviewRouteGroup::class,
            'settings' => [
                'include_unpublished_auth_user_reviews' => true,
                'auth_required' => true,
                'name_required' => false,
                'auth_middleware' => ['auth'],
            ],
        ],
    ],
];
