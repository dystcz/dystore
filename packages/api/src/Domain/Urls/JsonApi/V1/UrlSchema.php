<?php

namespace Dystore\Api\Domain\Urls\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
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
    public function fields(): array
    {
        return [
            $this->idField(),

            Str::make('slug'),

            Boolean::make('default'),

            ...parent::fields(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),

            Where::make('slug'),

            ...parent::filters(),
        ];
    }
}
