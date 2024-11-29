<?php

use Dystcz\LunarApi\Support\Config\Actions\RegisterRoutesFromConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => Config::get('lunar-api.general.route_prefix'),
    'middleware' => Config::get('lunar-api.general.route_middleware'),
], fn () => RegisterRoutesFromConfig::run('lunar-api.reviews.domains'));
