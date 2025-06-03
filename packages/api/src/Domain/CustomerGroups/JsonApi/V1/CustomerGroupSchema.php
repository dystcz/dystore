<?php

namespace Dystore\Api\Domain\CustomerGroups\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Str;
use Lunar\Models\Contracts\CustomerGroup;

class CustomerGroupSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = CustomerGroup::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [

        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            Str::make('name'),
            Str::make('handle'),
            Boolean::make('default'),
        ];
    }
}
