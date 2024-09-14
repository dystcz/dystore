<?php

namespace Dystcz\LunarApi\Domain\ProductVariants\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Eloquent\Fields\AttributeData;
use Dystcz\LunarApi\Domain\JsonApi\Eloquent\Schema;
use Dystcz\LunarApi\Support\Models\Actions\ModelType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\Map;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOne;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOneThrough;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\WhereHas;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Filters\WhereIdNotIn;
use Lunar\Models\Contracts\Attribute;
use Lunar\Models\Contracts\Price;
use Lunar\Models\Contracts\ProductOptionValue;
use Lunar\Models\Contracts\ProductVariant;
use Lunar\Models\Contracts\Url;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductVariantSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = ProductVariant::class;

    /**
     * Build an index query for this resource.
     */
    public function indexQuery(?Request $request, Builder $query): Builder
    {
        return $query->whereHas(
            'product',
            fn ($query) => $query->where('status', '!=', 'draft'),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function with(): array
    {
        return [
            'attributes',
            'attributes.attributeGroup',
            ...parent::with(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function includePaths(): iterable
    {
        return [
            'default-url',
            'images',
            'lowest-price',
            'prices',
            'thumbnail',
            'urls',
            'values',

            ...parent::includePaths(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function fields(): array
    {
        return [
            $this->idField(),

            AttributeData::make('attribute_data')
                ->groupAttributes(),

            Str::make('sku'),
            Str::make('gtin'),
            Str::make('mpn'),
            Str::make('ean'),
            Str::make('tax_ref'),

            Map::make('availability', [
                ArrayHash::make('stock')
                    ->extractUsing(
                        static fn (ProductVariant $model) => [
                            'quantity_string' => $model->approximateInStockQuantity,
                        ],
                    ),
                ArrayHash::make('status')
                    ->extractUsing(
                        static fn (ProductVariant $model) => $model->availability->toArray()
                    ),
            ]),

            BelongsTo::make('product'),

            HasMany::make('attributes', 'attributes')
                ->type(ModelType::get(Attribute::class))
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            HasMany::make('other-variants', 'otherVariants')
                ->type(ModelType::get(ProductVariant::class))
                ->canCount()
                ->countAs('other_variants_count'),

            HasMany::make('prices')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            HasOne::make('lowest-price', 'lowestPrice')
                ->type(ModelType::get(Price::class))
                ->retainFieldName(),

            HasOne::make('highest-price', 'highestPrice')
                ->type(ModelType::get(Price::class))
                ->retainFieldName(),

            HasMany::make('images', 'images')
                ->type(ModelType::get(Media::class))
                ->canCount(),

            HasMany::make('values', 'values')
                ->type(ModelType::get(ProductOptionValue::class))
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            HasOne::make('default-url', 'defaultUrl')
                ->type(ModelType::get(Url::class))
                ->retainFieldName(),

            HasMany::make('urls'),

            HasOneThrough::make('thumbnail')
                ->type(ModelType::get(Media::class)),

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

            WhereIdNotIn::make($this),

            WhereHas::make($this, 'urls', 'url')
                ->singular(),

            WhereHas::make($this, 'urls', 'urls'),

            ...parent::filters(),
        ];
    }
}
