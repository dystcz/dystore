<?php

namespace Dystore\ProductViews\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dystore\ProductViews\Skeleton\SkeletonClass
 */
class LunarApiProductViewsFacade extends Facade
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
