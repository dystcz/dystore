<?php

namespace Dystcz\LunarRewards\Facades;

use Illuminate\Support\Facades\Facade;

class LunarRewards extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lunar-rewards';
    }
}
