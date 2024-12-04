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
            'schema' => Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterSchema::class,
            'resource' => Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterResource::class,
            'query' => Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterQuery::class,
            'collection_query' => Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterCollectionQuery::class,
            'routes' => Dystore\Newsletter\Domain\Newsletter\Http\Routing\NewsletterRouteGroup::class,
            'route_actions' => [],
            'settings' => [],
        ],
    ],
];
