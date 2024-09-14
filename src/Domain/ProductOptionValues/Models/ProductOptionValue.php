<?php

namespace Dystcz\LunarApi\Domain\ProductOptionValues\Models;

use Dystcz\LunarApi\Domain\ProductOptionValues\Contracts\ProductOptionValue as ProductOptionValueContract;
use Dystcz\LunarApi\Domain\ProductOptionValues\Factories\ProductOptionValueFactory;
use Dystcz\LunarApi\Hashids\Traits\HashesRouteKey;
use Lunar\Models\ProductOptionValue as LunarProductOptionValue;

class ProductOptionValue extends LunarProductOptionValue implements ProductOptionValueContract
{
    use HashesRouteKey;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ProductOptionValueFactory
    {
        return ProductOptionValueFactory::new();
    }
}
