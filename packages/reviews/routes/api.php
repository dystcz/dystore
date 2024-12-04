<?php

use Dystore\Api\Support\Config\Actions\RegisterRoutesFromConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => Config::get('dystore.general.route_prefix'),
    'middleware' => Config::get('dystore.general.route_middleware'),
], fn () => RegisterRoutesFromConfig::run('dystore.reviews.domains'));
