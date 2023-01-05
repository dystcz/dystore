<?php

namespace Dystcz\LunarApi\Domain\ProductVariants\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use InvalidArgumentException;
use Lunar\Models\Price as LunarPrice;
use Lunar\Models\ProductVariant as LunarPoductVariant;

class ProductVariant extends LunarPoductVariant
{
    /**
     * Lowest price relation.
     *
     * @return MorphOne
     *
     * @throws InvalidArgumentException
     */
    public function lowestPrice(): MorphOne
    {
        return $this
            ->morphOne(
                LunarPrice::class,
                'priceable'
            )->ofMany('price', 'min');
    }
}
