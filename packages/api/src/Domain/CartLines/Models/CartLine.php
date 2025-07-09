<?php

namespace Dystore\Api\Domain\CartLines\Models;

use Dystore\Api\Domain\CartLines\Concerns\InteractsWithDystoreApi;
use Dystore\Api\Domain\CartLines\Contracts\CartLine as CartLineContract;
use Lunar\DataTypes\Price;
use Lunar\Models\CartLine as LunarCartLine;

/**
 * @property ?Price $unitPriceExTax
 * @property ?Price $unitTax
 */
class CartLine extends LunarCartLine implements CartLineContract
{
    use InteractsWithDystoreApi;

    /**
     * Unit price excluding tax.
     */
    public ?Price $unitPriceExTax = null;

    /**
     * Unit tax.
     */
    public ?Price $unitTax = null;
}
