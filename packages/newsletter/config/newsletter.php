<?php

/*
 * Lunar API Newsletter configuration
 */
return [
    // Configuration for specific domains
    'domains' => [
        'newsletters' => [
            'model' => null,
            'lunar_model' => null,
            'policy' => null,
            'schema' => Dystcz\LunarApiNewsletter\Domain\Newsletter\JsonApi\V1\NewsletterSchema::class,
            'resource' => Dystcz\LunarApiNewsletter\Domain\Newsletter\JsonApi\V1\NewsletterResource::class,
            'query' => Dystcz\LunarApiNewsletter\Domain\Newsletter\JsonApi\V1\NewsletterQuery::class,
            'collection_query' => Dystcz\LunarApiNewsletter\Domain\Newsletter\JsonApi\V1\NewsletterCollectionQuery::class,
            'routes' => Dystcz\LunarApiNewsletter\Domain\Newsletter\Http\Routing\NewsletterRouteGroup::class,
            'route_actions' => [],
            'settings' => [],
        ],
    ],
];
