<?php

namespace Dystore\Api\Base\Concerns;

use Dystore\Api\Support\Config\Actions\RegisterRoutesFromConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

trait HasRoutes
{
    public function routes(): static
    {
        Route::group([
            'prefix' => Config::get('dystore.general.route_prefix'),
            'middleware' => Config::get('dystore.general.route_middleware'),
        ], fn () => RegisterRoutesFromConfig::run());

        return $this;
    }
}
