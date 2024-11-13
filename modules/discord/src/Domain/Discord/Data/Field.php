<?php

namespace Modules\DystoreDiscord\Domain\Discord\Data;

use Illuminate\Contracts\Support\Arrayable;

class Field implements Arrayable
{
    public function __construct(
        public string $name,
        public string $value,
        public bool $inline = false,
    ) {
        //
    }

    /**
     * Cast the field to an array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'inline' => $this->inline,
        ];
    }
}
