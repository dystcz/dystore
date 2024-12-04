<?php

namespace Dystore\Api\Domain\Attributes\Models;

use Dystore\Api\Domain\Attributes\Concerns\InteractsWithDystoreApi;
use Dystore\Api\Domain\Attributes\Contracts\Attribute as AttributeContract;
use Lunar\Models\Attribute as LunarAttribute;

class Attribute extends LunarAttribute implements AttributeContract
{
    use InteractsWithDystoreApi;
}
