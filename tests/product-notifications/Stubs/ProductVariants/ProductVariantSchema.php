<?php

namespace Dystore\Tests\ProductNotifications\Stubs\ProductVariants;

use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsToMany;
use LaravelJsonApi\Eloquent\Schema;
use Lunar\Models\ProductVariant;

class ProductVariantSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = ProductVariant::class;

    /**
     * Get the JSON:API resource type.
     */
    public static function type(): string
    {
        return 'variants';
    }

    /**
     * Get the resource fields.
     */
    public static function defaultFields(): array
    {
        return [
            ID::make(),
            BelongsToMany::make('reviews'),
        ];
    }

    /**
     * Determine if the resource is authorizable.
     */
    public function authorizable(): bool
    {
        return false;
    }
}
