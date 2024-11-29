<?php

namespace Modules\DystoreDiscord\Domain\Discord\Data;

use Illuminate\Contracts\Support\Arrayable;

class Emoji implements Arrayable
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public bool $animated = false,
    ) {
        //
    }

    /**
     * Cast the emoji to an array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'animated' => $this->animated,
        ];
    }
}
