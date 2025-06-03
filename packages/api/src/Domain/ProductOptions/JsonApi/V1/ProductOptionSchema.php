<?php

namespace Dystore\Api\Domain\ProductOptions\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\ProductOption;
use Lunar\Models\Contracts\ProductOptionValue;

class ProductOptionSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = ProductOption::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            'product_option_values',
            'product_option_values.images',

        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            Str::make('name')
                ->readOnly()
                ->extractUsing(
                    fn (ProductOption $model, string $attribute) => $model->translate($attribute),
                ),

            Str::make('label')
                ->readOnly()
                ->extractUsing(
                    fn (ProductOption $model, string $attribute) => $model->translate($attribute),
                ),

            Str::make('handle')
                ->readOnly(),

            fn () => HasMany::make('product_option_values', 'values')
                ->type(SchemaType::get(ProductOptionValue::class))
                ->canCount()
                ->countAs('product_option_values_count')
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [];
    }
}
