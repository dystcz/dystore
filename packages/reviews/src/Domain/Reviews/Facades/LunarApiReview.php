<?php

namespace Dystore\Reviews\Domain\Reviews\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dystore\Reviews\Skeleton\SkeletonClass
 */
class LunarApiReview extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dystore-reviews';
    }
}
