<?php

namespace Dystore\Api\Domain\Auth\Http\Controllers;

use Dystore\Api\Base\Controller;
use Dystore\Api\Domain\Auth\JsonApi\V1\LoginRequest;
use Dystore\Api\Domain\Users\Contracts\User as UserContract;
use Dystore\Api\Domain\Users\JsonApi\V1\UserQuery;
use Dystore\Api\Domain\Users\JsonApi\V1\UserSchema;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Api\Facades\LunarApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelJsonApi\Core\Responses\DataResponse;

class AuthController extends Controller
{
    /**
     * Get currently logged in User.
     */
    public function me(
        UserSchema $schema,
        Request $request,
        UserQuery $query,
        UserContract $user,
    ): DataResponse {
        /** @var User|null $user */
        $user = $request->user();

        /** @var Order $model */
        $model = $schema
            ->repository()
            ->queryOne($user)
            ->withRequest($request)
            ->first();

        return DataResponse::make($model)
            ->withQueryParameters($query)
            ->didntCreate();
    }

    /**
     * Log the user in.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::guard(LunarApi::getAuthGuard())->attempt($request->only('email', 'password'))) {
            return new JsonResponse([
                'message' => __('lunar-api::validations.auth.attempt.failed'),
                'success' => false,
            ], 422);
        }

        return new JsonResponse([
            'message' => __('lunar-api::validations.auth.attempt.success'),
            'success' => true,
        ], 200);
    }

    /**
     * Log out logged in User.
     */
    public function logout(Request $request): DataResponse
    {
        Auth::guard(LunarApi::getAuthGuard())->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return DataResponse::make(null);
    }
}
