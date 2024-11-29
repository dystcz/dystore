<?php

namespace Modules\DystoreDiscord\Domain\Discord\Data;

use Illuminate\Contracts\Support\Arrayable;

class Image implements Arrayable
{
    public function __construct(
        public string $url,
        public ?string $proxyUrl = null,
        public ?int $height = null,
        public ?int $width = null,
    ) {
        //
    }

    /**
     * Cast the image to an array.
     *
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'proxy_url' => $this->proxyUrl,
            'height' => $this->height,
            'width' => $this->width,
        ];
    }
}
