<?php

namespace Dystore\Tests\Reviews\Stubs\Users;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\ID;

class UserSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = User::class;

    /**
     * Get the JSON:API resource type.
     */
    public static function type(): string
    {
        return 'users';
    }

    /**
     * Get the resource fields.
     */
    public static function defaultFields(): array
    {
        return [
            ID::make(),
        ];
    }
}
