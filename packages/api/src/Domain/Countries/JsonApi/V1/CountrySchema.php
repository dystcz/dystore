<?php

namespace Dystore\Api\Domain\Countries\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use Lunar\Models\Contracts\Country;

class CountrySchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Country::class;

    /**
     * {@inheritDoc}
     */
    public function fields(): array
    {
        return [
            $this->idField(),

            Str::make('name'),
            Str::make('iso2'),
            Str::make('iso3'),
            Str::make('phonecode'),
            Str::make('capital'),
            Str::make('currency'),
            Str::make('native'),
            Str::make('emoji'),
            Str::make('emoji_u'),

            ...parent::fields(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }
}
