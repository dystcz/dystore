<?php

namespace Dystore\Newsletter\Domain\Newsletter\Http\Routing;

use Dystcz\LunarApi\Routing\RouteGroup;
use Dystore\Newsletter\Domain\Newsletter\Http\Controllers\SubscribeToNewsletterController;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

class NewsletterRouteGroup extends RouteGroup
{
    public string $prefix = 'newsletters';

    /**
     * Register routes.
     */
    public function routes(): void
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
