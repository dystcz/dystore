<?php

namespace Dystcz\LunarApi\Domain\Countries\Models;

use Dystcz\LunarApi\Hashids\Traits\HashesRouteKey;
use Lunar\Models\Country as LunarCountry;

class Country extends LunarCountry
{
    use HashesRouteKey;
}
