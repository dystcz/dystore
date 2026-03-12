<?php

use Dystore\Api\Support\Models\Actions\SchemaType;
use Dystore\ProductNotifications\Domain\ProductNotifications\Http\Routing\ProductNotificationRouteGroup;
use Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationCollectionQuery;
use Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationQuery;
use Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationResource;
use Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema;
use Dystore\ProductNotifications\Domain\ProductNotifications\Models\ProductNotification;
use Dystore\ProductNotifications\Domain\ProductNotifications\Policies\ProductNotificationPolicy;

/*
 * Lunar API Product Notifications configuration
 */
return [
    // Configuration for specific domains
    'domains' => [
        SchemaType::get(ProductNotification::class) => [
            'model' => ProductNotification::class,
            'lunar_model' => null,
            'policy' => ProductNotificationPolicy::class,
            'schema' => ProductNotificationSchema::class,
            'resource' => ProductNotificationResource::class,
            'query' => ProductNotificationQuery::class,
            'collection_query' => ProductNotificationCollectionQuery::class,
            'routes' => ProductNotificationRouteGroup::class,
        ],
    ],
];
