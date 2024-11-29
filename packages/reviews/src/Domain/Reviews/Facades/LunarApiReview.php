<?php

namespace Dystcz\LunarApiReviews\Domain\Reviews\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dystcz\LunarApiReviews\Skeleton\SkeletonClass
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
        return 'lunar-api-reviews';
    }
}
