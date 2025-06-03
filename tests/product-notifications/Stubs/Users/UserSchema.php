<?php

namespace Dystore\Tests\ProductNotifications\Stubs\Users;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
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
    public static function type(): string
    {
        return 'users';
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            ID::make(),
        ];
    }
}
