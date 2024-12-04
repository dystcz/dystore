<?php

namespace Dystore\Newsletter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dystore\Newsletter\Newsletter
 */
class Newsletter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dystore-newsletter';
    }
}
