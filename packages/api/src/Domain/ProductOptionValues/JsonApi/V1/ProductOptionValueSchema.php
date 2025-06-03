<?php

namespace Dystore\Api\Domain\ProductOptionValues\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\ProductOption;
use Lunar\Models\Contracts\ProductOptionValue;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductOptionValueSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = ProductOptionValue::class;

    /**
     * {@inheritDoc}
     */
    public static function defaultWith(): array
    {
        return [
            'option',

        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            'images',
            'product_option',

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
                    fn (ProductOptionValue $model, string $attribute) => $model->translate($attribute),
                ),

            Str::make('product_option_handle', 'handle')
                ->readOnly()
                ->on('option'),

            fn () => BelongsTo::make('product_option', 'option')
                ->readOnly()
                ->type(SchemaType::get(ProductOption::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            fn () => HasMany::make('images', 'images')
                ->type(SchemaType::get(Media::class))
                ->canCount()
                ->countAs('images_count')
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
