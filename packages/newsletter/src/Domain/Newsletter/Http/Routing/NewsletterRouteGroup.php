<?php

namespace Dystcz\LunarApiNewsletter\Domain\Newsletter\Http\Routing;

use Dystcz\LunarApi\Routing\RouteGroup;
use Dystcz\LunarApiNewsletter\Domain\Newsletter\Http\Controllers\SubscribeToNewsletterController;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

class NewsletterRouteGroup extends RouteGroup
{
    public string $prefix = 'newsletters';

    public array $middleware = [];

    /**
     * Register routes.
     */
    public function routes(string $prefix = null, array|string $middleware = []): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function (ResourceRegistrar $server) {
                $server->resource($this->getPrefix(), SubscribeToNewsletterController::class)
                    ->only('')
                    ->actions('-actions', function ($actions) {
                        $actions->post('subscribe');
                    });
            });
    }
}
