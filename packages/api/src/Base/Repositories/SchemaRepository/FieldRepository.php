<?php

namespace Dystore\Api\Base\Repositories\SchemaRepository;

use Closure;
use Dystore\Api\Base\Contracts\Extendable;
use Dystore\Api\Base\Repositories\Repository;

class FieldRepository extends Repository
{
    // public function resolve(?Extendable $extendable = null): iterable
    // {
    //     foreach ($this->items as $key => $value) {
    //         if ($value instanceof Closure) {
    //             $value = Closure::bind($value, $extendable, get_parent_class($extendable));
    //
    //             yield $value($extendable);
    //         }
    //
    //         yield $value;
    //     }
    // }
}
