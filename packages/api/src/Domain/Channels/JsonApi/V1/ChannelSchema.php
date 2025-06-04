<?php

namespace Dystore\Api\Domain\Channels\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use Lunar\Models\Contracts\Channel;

class ChannelSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Channel::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [];
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
            Str::make('url'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [
            Where::make('handle'),
        ];
    }
}
