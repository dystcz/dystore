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
            'schema' => Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewSchema::class,

            // Route groups which get registered
            // If you want to change the behaviour or add some data,
            // simply extend the package product groups and add your logic
            'route_groups' => [
                'reviews' => Dystcz\LunarApiReviews\Domain\Reviews\Http\Routing\ReviewRouteGroup::class,
            ],
        ],
    ],

    // Middleware for specific guarded routes
    'auth_middleware' => ['auth'],

    // Pagination defaults
    'pagination' => [
        'per_page' => 12,
        'max_size' => 25,
    ],
];
