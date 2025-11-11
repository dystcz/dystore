<?php

namespace Dystore\Api\Domain\CartLines\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Fields\CartLinePricing;
use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\MorphTo;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\CartLine;
use Lunar\Models\Contracts\Product;
use Lunar\Models\Contracts\ProductVariant;

class CartLineSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = CartLine::class;

    /**
     * {@inheritDoc}
     */
    public function includePaths(): iterable
    {
        return [
            'purchasable',
            'purchasable.images',
            'purchasable.prices',
            'purchasable.price',
            'purchasable.lowest_price',
            'purchasable.highest_price',
            'purchasable.thumbnail',
            'purchasable.product',
            'purchasable.product.images',
            'purchasable.product.thumbnail',
            'purchasable.product_option_values',
            'purchasable.product_option_values.images',
            'purchasable.product_option_values.product_option',

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

            Number::make('purchasable_id'),

            Str::make('purchasable_type'),

            Number::make('quantity'),

            CartLinePricing::make('pricing'),

            ArrayHash::make('meta'),

            BelongsTo::make('cart')
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            MorphTo::make('purchasable', 'purchasable')
                ->types(
                    SchemaType::get(Product::class),
                    SchemaType::get(ProductVariant::class),
                )
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            ...parent::fields(),
        ];
    }
}
