<?php

namespace Dystcz\LunarApiReviews\Tests\Stubs\ProductVariants;

use Dystcz\LunarApi\Domain\JsonApi\Eloquent\Schema;
use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;

class ProductVariantSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = ProductVariant::class;

    /**
     * Get the resource fields.
     */
    public function fields(): array
    {
        return [
            ID::make(),
            HasMany::make('reviews'),
        ];
    }

    /**
     * Determine if the resource is authorizable.
     */
    public function authorizable(): bool
    {
        return false;
    }

    /**
     * Get the JSON:API resource type.
     */
    public static function type(): string
    {
        return 'variants';
    }
}
