<?php

namespace Dystore\Api\Domain\Users\Actions;

use Dystore\Api\Domain\Users\Contracts\CreatesNewUsers;
use Dystore\Api\Domain\Users\Contracts\RegistersUser;
use Dystore\Api\Domain\Users\Contracts\UserData as UserDataContract;
use Dystore\Api\Domain\Users\Data\UserData;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class RegisterUser implements RegistersUser
{
    public function __construct(
        protected CreatesNewUsers $createUser,
    ) {}

    /**
     * Create a newly registered user or return the existing one.
     *
     * @param  array<string, string>  $data
     */
    public function register(UserDataContract $data): Authenticatable
    {
        $existing = $this->findExistingUser($data);

        if ($existing) {
            return $existing;
        }

        $user = $this->createUser($data);

        Event::dispatch(new Registered($user));

        return $user;
    }

    /**
     * Find an existing user by email.
     */
    protected function findExistingUser(UserDataContract $data): ?Authenticatable
    {
        /** @var class-string<Authenticatable> $model */
        $model = Config::get('auth.providers.users.model');

        return $model::query()
            ->where('email', $data->email())
            ->first();
    }

    /**
     * Create a new user instance.
     *
     * @param  array<string, string>  $data
     */
    protected function createUser(UserDataContract $data): Authenticatable
    {
        $hasPassword = $data->password() !== null;

        $data = new UserData(
            name: $data->name(),
            email: $data->email(),
            password: $data->password() ?? Str::random(32),
        );

        $user = $this->createUser->create($data);

        $user->forceFill(['password_set' => $hasPassword])->save();

        return $user;
    }
}
