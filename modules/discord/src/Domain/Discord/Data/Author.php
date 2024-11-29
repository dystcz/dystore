<?php

namespace Modules\DystoreDiscord\Domain\Discord\Data;

use Illuminate\Contracts\Support\Arrayable;

class Author implements Arrayable
{
    public function __construct(
        public string $name,
        public ?string $url = null,
        public ?string $icon_url = null,
        public ?string $proxy_icon_url = null,
    ) {
        //
    }

    /**
     * Cast the author to an array.
     *
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'icon_url' => $this->icon_url,
            'proxy_icon_url' => $this->proxy_icon_url,
        ];
    }
}
