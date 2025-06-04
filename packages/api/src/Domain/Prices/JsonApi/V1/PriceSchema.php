<?php

namespace Dystore\Api\Domain\Prices\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Domain\Prices\Actions\GetComparePriceDiscount;
use Dystore\Api\Domain\Prices\Actions\GetPrice;
use Dystore\Api\Domain\Prices\JsonApi\Filters\MaxPriceFilter;
use Dystore\Api\Domain\Prices\JsonApi\Filters\MinPriceFilter;
use Dystore\Api\Support\Models\Actions\SchemaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Http\Request;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Map;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Resources\Relation;
use Lunar\Models\Contracts\Currency;
use Lunar\Models\Contracts\CustomerGroup;
use Lunar\Models\Contracts\Price;

class PriceSchema extends Schema
{
    /**
     * {@inheritDoc}
     */
    public static string $model = Price::class;

    /**
     * Build an index query for this resource.
     */
    public function indexQuery(?Request $request, Builder $query): Builder
    {
        /** @var \Dystore\Api\Domain\Prices\Builders\PriceBuilder $query */
        return $query;
    }

    /**
     * Build a "relatable" query for this resource.
     */
    public function relatableQuery(?Request $request, EloquentRelation $query): EloquentRelation
    {
        /** @var \Dystore\Api\Domain\Prices\Builders\PriceBuilder $query */
        return $query;
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultWith(): array
    {
        return [
            'currency',

        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            'currency',
            'customer_group',

        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            static::idField(),

            Map::make('base_price', [
                Str::make('formatted')->extractUsing(static fn (Price $model) => (new GetPrice)($model)->formatted()),
                Number::make('decimal')->extractUsing(static fn (Price $model) => (new GetPrice)($model)->decimal()),
                Number::make('value')->extractUsing(static fn (Price $model) => (new GetPrice)($model)->value),
            ]),

            Map::make('sub_price', [
                Str::make('formatted')->extractUsing(static fn (Price $model) => $model->priceExTax()->formatted()),
                Number::make('decimal')->extractUsing(static fn (Price $model) => $model->priceExTax()->decimal()),
                Number::make('value')->extractUsing(static fn (Price $model) => $model->priceExTax()->value),
            ]),

            Map::make('compare_price', [
                Str::make('formatted')->extractUsing(static fn (Price $model) => (new GetPrice)($model, 'compare_price')->formatted()),
                Number::make('decimal')->extractUsing(static fn (Price $model) => (new GetPrice)($model, 'compare_price')->decimal()),
                Number::make('value')->extractUsing(static fn (Price $model) => (new GetPrice)($model, 'compare_price')->value),
            ]),

            Map::make('discount', [
                Number::make('formatted')
                    ->extractUsing(static function (Price $model) {
                        $price = (new GetPrice)($model);
                        $comparePrice = (new GetPrice)($model, 'compare_price');

                        return (new GetComparePriceDiscount($price, $comparePrice))->formatted();
                    }),
                Number::make('decimal')
                    ->extractUsing(static function (Price $model) {
                        $price = (new GetPrice)($model);
                        $comparePrice = (new GetPrice)($model, 'compare_price');

                        return (new GetComparePriceDiscount($price, $comparePrice))->decimal();
                    }),
                Number::make('value')
                    ->extractUsing(static function (Price $model) {
                        $price = (new GetPrice)($model);
                        $comparePrice = (new GetPrice)($model, 'compare_price');

                        return (new GetComparePriceDiscount($price, $comparePrice))->value();
                    }),
                Boolean::make('on_sale')
                    ->extractUsing(static function (Price $model) {
                        $price = (new GetPrice)($model);
                        $comparePrice = (new GetPrice)($model, 'compare_price');

                        return (new GetComparePriceDiscount($price, $comparePrice))->isOnSale();
                    }),
            ]),

            BelongsTo::make('currency')
                ->type(SchemaType::get(Currency::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),

            BelongsTo::make('customer_group', 'customerGroup')
                ->type(SchemaType::get(CustomerGroup::class))
                ->serializeUsing(static fn (Relation $relation) => $relation->withoutLinks()),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [
            MinPriceFilter::make('min_price', 'price'),
            MaxPriceFilter::make('max_price', 'price'),
        ];
    }
}
