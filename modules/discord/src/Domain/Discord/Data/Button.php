<?php

namespace Modules\DystoreDiscord\Domain\Discord\Data;

use Modules\DystoreDiscord\Domain\Discord\Enums\ButtonStyle;

class Button extends Component
{
    const COMPONENT_TYPE = 2;

    public function __construct(
        public ButtonStyle $style,
        public ?string $label = null,
        public ?Emoji $emoji = null,
        public ?string $custom_id = null,
        public ?string $url = null,
        public bool $disabled = false,
    ) {
        //
    }

    /**
     * Cast the button to an array.
     */
    public function toArray(): array
    {
        return [
            'type' => self::COMPONENT_TYPE,
            'style' => $this->style->value,
            'label' => $this->label,
            'emoji' => $this->emoji?->toArray(),
            'custom_id' => $this->custom_id,
            'url' => $this->url,
            'disabled' => $this->disabled,
        ];
    }
}
