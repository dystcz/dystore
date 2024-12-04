<?php

namespace Dystore\Api\Domain\ProductOptionValues\Models;

use Dystore\Api\Domain\ProductOptionValues\Concerns\InteractsWithDystoreApi;
use Dystore\Api\Domain\ProductOptionValues\Contracts\ProductOptionValue as ProductOptionValueContract;
use Lunar\Models\ProductOptionValue as LunarProductOptionValue;

class ProductOptionValue extends LunarProductOptionValue implements ProductOptionValueContract
{
    use InteractsWithDystoreApi;
}
