<?php

use Dystore\Api\Support\Models\Actions\SchemaType;

/*
 * Lunar API Reviews Configuration
 */
return [
    // Configuration for specific domains
    'domains' => [
        SchemaType::get(Dystore\Reviews\Domain\Reviews\Models\Review::class) => [
            'model' => Dystore\Reviews\Domain\Reviews\Models\Review::class,
            'lunar_model' => null,
            'policy' => Dystore\Reviews\Domain\Reviews\Policies\ReviewPolicy::class,
            'schema' => Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewSchema::class,
            'resource' => Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewResource::class,
            'query' => Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewQuery::class,
            'collection_query' => Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewCollectionQuery::class,
            'routes' => Dystore\Reviews\Domain\Reviews\Http\Routing\ReviewRouteGroup::class,
            'settings' => [
                'include_unpublished_auth_user_reviews' => true,
                'auth_required' => true,
                'name_required' => false,
                'auth_middleware' => ['auth'],
            ],
        ],
    ],
];
