<?php

namespace Dystcz\LunarApi\Domain\Auth\Http\Controllers;

use Dystcz\LunarApi\Base\Controller;
use Dystcz\LunarApi\Domain\Auth\JsonApi\V1\LoginRequest;
use Dystcz\LunarApi\Domain\Users\Contracts\User as UserContract;
use Dystcz\LunarApi\Domain\Users\JsonApi\V1\UserQuery;
use Dystcz\LunarApi\Domain\Users\JsonApi\V1\UserSchema;
use Dystcz\LunarApi\Domain\Users\Models\User;
use Dystcz\LunarApi\Facades\LunarApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelJsonApi\Core\Responses\DataResponse;

class AuthController extends Controller
{
    /**
     * Get currently logged in User.
     */
    public function me(UserSchema $schema, Request $request, UserQuery $query, UserContract $user): DataResponse
    {
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
     *
     * NOTE: Login attempt is in the request class
     */
    public function login(UserSchema $schema, LoginRequest $request, UserQuery $query, UserContract $user): DataResponse
    {
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
