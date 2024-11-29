<?php

namespace Modules\DystoreDiscord\Domain\Discord\Data;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Embed implements Arrayable
{
    /**
     * @param  Collection<int,Field>  $fields
     */
    public function __construct(
        public ?string $title = null,
        public ?string $type = null,
        public ?string $description = null,
        public ?string $url = null,
        public ?int $color = null,
        public ?Footer $footer = null,
        public ?Image $image = null,
        public ?Image $thumbnail = null,
        public ?Author $author = null,
        public ?Carbon $timestamp = null,
        public ?Collection $fields = null,
    ) {
        $this->fields = $fields ?? Collection::make();
    }

    /**
     * Cast the embed to an array.
     *
     * @param  Field  $field
     * @return $this
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'type' => $this->type,
            'description' => $this->description,
            'url' => $this->url,
            'color' => $this->color,
            'footer' => $this->footer?->toArray(),
            'image' => $this->image?->toArray(),
            'thumbnail' => $this->thumbnail?->toArray(),
            'author' => $this->author?->toArray(),
            'timestamp' => $this->timestamp?->toIso8601String(),
            'fields' => $this->fields->map(fn (Field $field) => $field->toArray())->toArray(),
        ];
    }
}
