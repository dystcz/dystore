<?php

use Dystcz\LunarApi\Support\Models\Actions\SchemaType;

/*
 * Lunar API Product Notifications configuration
 */
return [
    // Configuration for specific domains
    'domains' => [
        SchemaType::get(Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Models\ProductNotification::class) => [
            'model' => Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Models\ProductNotification::class,
            'lunar_model' => null,
            'policy' => Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Policies\ProductNotificationPolicy::class,
            'schema' => Dystcz\LunarApiProductNotification\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema::class,
            'resource' => Dystcz\LunarApiProductNotification\Domain\ProductNotifications\JsonApi\V1\ProductNotificationResource::class,
            'query' => Dystcz\LunarApiProductNotification\Domain\ProductNotifications\JsonApi\V1\ProductNotificationQuery::class,
            'collection_query' => Dystcz\LunarApiProductNotification\Domain\ProductNotifications\JsonApi\V1\ProductNotificationCollectionQuery::class,
            'routes' => Dystcz\LunarApiProductNotification\Domain\ProductNotifications\Http\Routing\ProductNotificationRouteGroup::class,
        ],
    ],
];
