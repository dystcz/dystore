<?php

use Dystore\Api\Support\Models\Actions\SchemaType;
use Dystore\Reviews\Domain\Reviews\Http\Routing\ReviewRouteGroup;
use Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewCollectionQuery;
use Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewQuery;
use Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewResource;
use Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewSchema;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Dystore\Reviews\Domain\Reviews\Policies\ReviewPolicy;

/*
 * Lunar API Reviews Configuration
 */
return [
    // Configuration for specific domains
    'domains' => [
        SchemaType::get(Review::class) => [
            'model' => Review::class,
            'model_contract' => Dystore\Reviews\Domain\Reviews\Contacts\Review::class,
            'policy' => ReviewPolicy::class,
            'schema' => ReviewSchema::class,
            'resource' => ReviewResource::class,
            'query' => ReviewQuery::class,
            'collection_query' => ReviewCollectionQuery::class,
            'routes' => ReviewRouteGroup::class,
            'settings' => [
                'include_unpublished_auth_user_reviews' => true,
                'auth_required' => true,
                'rating_required' => false,
                'name_required' => false,
                'purchasable_required' => false,
                'auth_middleware' => ['auth'],
            ],
        ],
    ],
];
