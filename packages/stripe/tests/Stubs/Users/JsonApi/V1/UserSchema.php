<?php

namespace Dystcz\LunarApiStripeAdapter\Tests\Stubs\Users\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Eloquent\Schema;
use Dystcz\LunarApiStripeAdapter\Tests\Stubs\Users\User;
use LaravelJsonApi\Eloquent\Fields\ID;

class UserSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = User::class;

    /**
     * {@inheritDoc}
     */
    public function fields(): array
    {
        return [
            ID::make(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function authorizable(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public static function type(): string
    {
        return 'users';
    }
}
