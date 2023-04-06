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
        'reviews' => [
            'model' => Dystcz\LunarApiReviews\Domain\Reviews\Models\Review::class,

            // Route groups which get registered
            // If you want to change the behaviour or add some data,
            // simply extend the package product groups and add your logic
            'route_groups' => [
                'reviews' => Dystcz\LunarApiReviews\Domain\Reviews\Http\Routing\ReviewRouteGroup::class,
            ],

            // Default pagination
            'pagination' => 12,
        ],
    ],

    // Middleware for specific guarded routes
    'auth_middleware' => ['auth'],
];
