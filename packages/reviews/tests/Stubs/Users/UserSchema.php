<?php

namespace Dystcz\LunarApiReviews\Tests\Stubs\Users;

use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Schema;

class UserSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = User::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
        ];
    }

    /**
     * Get the JSON:API resource type.
     *
     * @return string
     */
    public static function type(): string
    {
        return 'users';
    }
}
