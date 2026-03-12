<?php

use Dystore\Newsletter\Domain\Newsletter\Http\Routing\NewsletterRouteGroup;
use Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterCollectionQuery;
use Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterQuery;
use Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterResource;
use Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterSchema;

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
            'schema' => NewsletterSchema::class,
            'resource' => NewsletterResource::class,
            'query' => NewsletterQuery::class,
            'collection_query' => NewsletterCollectionQuery::class,
            'routes' => NewsletterRouteGroup::class,
            'route_actions' => [],
            'settings' => [],
        ],
    ],
];
