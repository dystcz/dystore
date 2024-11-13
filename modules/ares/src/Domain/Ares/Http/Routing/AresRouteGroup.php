<?php

namespace Modules\DystoreAres\Domain\Ares\Http\Routing;

use Dystcz\LunarApi\Routing\Contracts\RouteGroup as RouteGroupContract;
use Dystcz\LunarApi\Routing\RouteGroup;
use Dystcz\LunarApi\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\ActionRegistrar;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;
use Modules\DystoreAres\Domain\Ares\Contracts\AresController;
use Modules\DystoreAres\Domain\Ares\Entities\AresRecord;

class AresRouteGroup extends RouteGroup implements RouteGroupContract
{
    /**
     * Register routes.
     */
    public function routes(): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function (ResourceRegistrar $server) {
                $server
                    ->resource(SchemaType::get(AresRecord::class), AresController::class)
                    ->only('show', 'index');

                $server
                    ->resource(SchemaType::get(AresRecord::class), AresController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions
                            ->post('get-company-info')
                            ->name('ares.get-company-info');
                    });
            });
    }
}
