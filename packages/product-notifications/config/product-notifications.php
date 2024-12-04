<?php

use Dystore\Api\Support\Models\Actions\SchemaType;

/*
 * Lunar API Product Notifications configuration
 */
return [
    // Configuration for specific domains
    'domains' => [
        SchemaType::get(Dystore\ProductNotifications\Domain\ProductNotifications\Models\ProductNotification::class) => [
            'model' => Dystore\ProductNotifications\Domain\ProductNotifications\Models\ProductNotification::class,
            'lunar_model' => null,
            'policy' => Dystore\ProductNotifications\Domain\ProductNotifications\Policies\ProductNotificationPolicy::class,
            'schema' => Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema::class,
            'resource' => Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationResource::class,
            'query' => Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationQuery::class,
            'collection_query' => Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationCollectionQuery::class,
            'routes' => Dystore\ProductNotifications\Domain\ProductNotifications\Http\Routing\ProductNotificationRouteGroup::class,
        ],
    ],
];
