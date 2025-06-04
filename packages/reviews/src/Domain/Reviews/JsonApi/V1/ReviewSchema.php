<?php

namespace Dystore\Reviews\Domain\Reviews\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Eloquent\Schema;
use Dystore\Api\Domain\Products\JsonApi\V1\ProductSchema;
use Dystore\Api\Domain\ProductVariants\JsonApi\V1\ProductVariantSchema;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\MorphTo;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Sorting\SortColumn;

class ReviewSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = Review::class;

    /**
     * Default sort.
     */
    protected $defaultSort = '-id';

    /**
     * {@inheritDoc}
     */
    public static function defaultWith(): array
    {
        return [
            'user',
            'user.customers',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [
            'user',
            'user.customers',
        ];
    }

    /**
     * Build an index query for this resource.
     */
    public function indexQuery(?Request $request, Builder $query): Builder
    {
        /** @var \Dystore\Reviews\Domain\Reviews\Builders\ReviewBuilder $query */
        return $query->published();
    }

    /**
     * Get the resource fields.
     */
    public static function defaultFields(): iterable
    {
        return [
            static::idField(),

            Str::make('name')
                ->extractUsing(
                    fn ($model, $column, $value) => $model->name,
                ),

            Str::make('comment'),

            Number::make('rating')
                ->sortable(),

            Number::make('purchasable_id'),

            Str::make('purchasable_type'),

            ArrayHash::make('meta'),

            DateTime::make('published_at')
                ->serializeUsing(
                    static fn ($value) => $value?->format('Y-m-d H:i:s'),
                )
                ->sortable(),

            BelongsTo::make('user')
                ->serializeUsing(
                    static fn ($relation) => $relation->withoutLinks(),
                ),

            MorphTo::make('purchasable', 'reviews')
                ->types(
                    ProductSchema::type(),
                    ProductVariantSchema::type(),
                ),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultSortables(): array
    {
        return [
            SortColumn::make('id', 'id'),

            SortColumn::make('published_at', 'published_at'),
        ];
    }
}
