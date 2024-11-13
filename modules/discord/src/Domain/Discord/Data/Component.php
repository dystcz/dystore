<?php

namespace Modules\DystoreDiscord\Domain\Discord\Data;

use Illuminate\Contracts\Support\Arrayable;
use Modules\DystoreDiscord\Domain\Discord\Enums\ComponentType;

abstract class Component implements Arrayable
{
    public function __construct(
        public ComponentType $type,
    ) {
        //
    }

    /**
     * Cast the component to an array.
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
        ];
    }
}
