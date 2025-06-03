<?php

namespace Dystore\ProductViews\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool enabled()
 * @method static array getLists()
 * @method static array sorted()
 * @method static void record(int $productId)
 * @method static void removeOldEntries()
 *
 * @see \Dystore\ProductViews\ProductViews
 */
class ProductViews extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dystore-product-views';
    }
}
