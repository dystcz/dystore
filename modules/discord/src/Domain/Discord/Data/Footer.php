<?php

namespace Modules\DystoreDiscord\Domain\Discord\Data;

use Illuminate\Contracts\Support\Arrayable;

class Footer implements Arrayable
{
    public function __construct(
        public string $text,
        public ?string $icon_url = null,
        public ?string $proxy_icon_url = null,
    ) {
        //
    }

    /**
     * Cast the footer to an array.
     *
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'icon_url' => $this->icon_url,
            'proxy_icon_url' => $this->proxy_icon_url,
        ];
    }
}
