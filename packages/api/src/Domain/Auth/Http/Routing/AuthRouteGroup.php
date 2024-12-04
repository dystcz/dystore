<?php

namespace Dystore\Api\Domain\Auth\Http\Routing;

use Dystore\Api\Domain\Auth\Contracts\AuthController;
use Dystore\Api\Domain\Auth\Contracts\AuthUserOrdersController;
use Dystore\Api\Domain\Auth\Contracts\PasswordResetLinkController;
use Dystore\Api\Domain\Auth\Contracts\RegisterUserWithoutPasswordController;
use Dystore\Api\Domain\Auth\Http\Controllers\NewPasswordController;
use Dystore\Api\Facades\Api;
use Dystore\Api\Routing\Contracts\RouteGroup as RouteGroupContract;
use Dystore\Api\Routing\RouteGroup;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\ActionRegistrar;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

class AuthRouteGroup extends RouteGroup implements RouteGroupContract
{
    /**
     * Register routes.
     */
    public function routes(): void
    {
        JsonApiRoute::server('v1')
            ->prefix('v1')
            ->resources(function (ResourceRegistrar $server) {
                $authGuard = Api::getAuthGuard();

                $server
                    ->resource('auth', AuthController::class)
                    ->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions
                            ->get('me')
                            ->name('auth.me');
                        $actions
                            ->post('logout')
                            ->name('auth.logout');
                        $actions
                            ->post('login')
                            ->name('auth.login')
                            ->withoutMiddleware('auth:'.Api::getAuthGuard());
                    })->middleware('auth:'.Api::getAuthGuard());

                $server->resource('auth', AuthUserOrdersController::class)->only('')
                    ->actions('-actions/me', function (ActionRegistrar $actions) {
                        $actions->get('orders', 'index')->name('my-orders');
                    })
                    ->middleware('auth:'.Api::getAuthGuard());

                $server->resource('auth', RegisterUserWithoutPasswordController::class)->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->post('register-without-password');
                    })
                    ->middleware('guest:'.Api::getAuthGuard());

                $server->resource('auth', PasswordResetLinkController::class)->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions->post('forgot-password');
                    })
                    ->middleware('guest:'.Api::getAuthGuard());

                $server->resource('auth', NewPasswordController::class)->only('')
                    ->actions('-actions', function (ActionRegistrar $actions) {
                        $actions
                            ->post('reset-password')
                            ->name('users.passwords.reset');
                        $actions
                            ->get('reset-password/{token}', 'create')
                            ->name('users.passwords.set-new-password');
                    })
                    ->middleware('guest:'.Api::getAuthGuard());
            });
    }
}
