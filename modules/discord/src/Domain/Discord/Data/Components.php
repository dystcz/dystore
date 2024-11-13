<?php

namespace Modules\DystoreDiscord\Domain\Discord\Data;

use Illuminate\Support\Collection;
use Modules\DystoreDiscord\Domain\Discord\Enums\ComponentType;

class Components extends Component
{
    public function __construct(
        public ComponentType $type,
        public Collection $components,
    ) {
        $this->components = $components ?? Collection::make();
    }

    /**
     * Cast the components to an array.
     *
     * @param  Field  $field
     * @return $this
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'components' => $this->components->map(
                fn (Component $component) => $component,
            )->toArray(),
        ];
    }
}
