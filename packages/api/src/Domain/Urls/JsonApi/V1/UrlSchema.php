<?php

namespace Dystore\Api\Domain\Urls\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use Lunar\Models\Contracts\Url;

class UrlSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Url::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            Str::make('slug'),
            Boolean::make('default'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [
            Where::make('slug'),
        ];
    }
}
