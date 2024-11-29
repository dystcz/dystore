<?php

namespace Dystcz\LunarApiNewsletter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dystcz\LunarApiNewsletter\Skeleton\SkeletonClass
 */
class LunarApiNewsletterFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lunar-api-newsletter';
    }
}
