<?php

namespace Dystore\Api\Domain\OrderLines\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Fields\OrderLinePricing;
use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Domain\ShippingOptions\Entities\ShippingOption;
use Dystore\Api\Support\Models\Actions\SchemaType;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\MorphTo;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\OrderLine;
use Lunar\Models\Contracts\Product;
use Lunar\Models\Contracts\ProductVariant;

class OrderLineSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = OrderLine::class;

    /**
     * The default paging parameters to use if the client supplies none.
     */
    protected ?array $defaultPagination = ['number' => 1];

    /**
     * {@inheritDoc}
     */
    public function includePaths(): iterable
    {
        return [
            'currency',

            'order',

            'purchasable',
            'purchasable.images',
            'purchasable.prices',
            'purchasable.price',
            'purchasable.lowest_price',
            'purchasable.highest_price',
            'purchasable.product',
            'purchasable.product.thumbnail',

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

            Str::make('type'),
            Str::make('description'),
            Str::make('option'),
            Str::make('identifier'),
            Str::make('notes'),

            OrderLinePricing::make('pricing'),

            ArrayHash::make('meta'),

            BelongsTo::make('order')
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            BelongsTo::make('currency')
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            MorphTo::make('purchasable', 'purchasable')
                ->types(
                    SchemaType::get(Product::class),
                    SchemaType::get(ProductVariant::class),
                    SchemaType::get(ShippingOption::class),
                )
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            ...parent::fields(),
        ];
    }
}
