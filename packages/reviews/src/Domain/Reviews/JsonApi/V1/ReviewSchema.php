<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Eloquent\Schema;
use Dystcz\LunarApiReviews\Domain\Reviews\Builders\ReviewBuilder;
use Dystcz\LunarApiReviews\Domain\Reviews\Models\Review;
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
     * {@inheritDoc}
     */
    protected array $with = [
        'user',
        'user.customers',
    ];

    /**
     * Default sort.
     */
    protected $defaultSort = '-id';

    /**
     * {@inheritDoc}
     */
    public function includePaths(): iterable
    {
        return [
            'user',
            'user.customers',

            ...parent::includePaths(),
        ];
    }

    /**
     * Build an index query for this resource.
     */
    public function indexQuery(?Request $request, Builder $query): Builder
    {
        /** @var ReviewBuilder $query */
        return $query->published();
    }

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): iterable
    {
        return [
            $this->idField(),

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
                ->types('products', 'variants'),

            ...parent::fields(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function sortables(): iterable
    {
        return [
            ...parent::sortables(),

            SortColumn::make('id', 'id'),

            SortColumn::make('publishedAt', 'published_at'),
        ];
    }

    /**
     * Get the JSON:API resource type.
     */
    public static function type(): string
    {
        return 'reviews';
    }
}
