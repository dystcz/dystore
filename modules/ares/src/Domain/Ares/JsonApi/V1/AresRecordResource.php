<?php

namespace Modules\DystoreAres\Domain\Ares\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Resources\JsonApiResource;

class AresRecordResource extends JsonApiResource
{
    /**
     * {@inheritDoc}
     */
    public function id(): string
    {
        return $this->resource->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function attributes($request): iterable
    {
        /** @var AresRecord $record */
        $record = $this->resource;

        return $record->toArray();
    }
}
