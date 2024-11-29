<?php

namespace Dystcz\LunarNewsletter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dystcz\LunarNewsletter\Skeleton\SkeletonClass
 */
class LunarNewsletterFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lunar-newsletter';
    }
}
