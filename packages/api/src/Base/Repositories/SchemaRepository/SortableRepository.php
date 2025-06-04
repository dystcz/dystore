<?php

namespace Dystore\Api\Base\Repositories\SchemaRepository;

use Dystore\Api\Base\Contracts\Extendable;
use Dystore\Api\Base\Repositories\Repository;

class SortableRepository extends Repository
{
    public function resolve(?Extendable $extendable = null): iterable
    {
        foreach ($this->items as $key => $value) {
            yield clone $value;
        }
    }
}
