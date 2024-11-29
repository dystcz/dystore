<?php

/*
 * You can place your custom package configuration in here.
 */
return [

    // Prefix for all the API routes
    // Leave empty if you don't want to use a prefix
    'route_prefix' => 'api',

    // Middleware for all the API routes
    'route_middleware' => ['api'],

    // Configuration for specific domains
    'domains' => [
        'product-notifications' => [
            'model' => Dystcz\LunarProductNotification\Domain\ProductNotifications\Models\ProductNotification::class,

            // Route groups which get registered
            // If you want to change the behaviour or add some data,
            // simply extend the package product groups and add your logic
            'route_groups' => [
                'product-notifications' => Dystcz\LunarProductNotification\Domain\ProductNotifications\Http\Routing\ProductNotificationRouteGroup::class,
            ],
        ],
    ],
];
