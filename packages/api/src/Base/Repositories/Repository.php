<?php

namespace Dystore\Api\Base\Repositories;

use Closure;
use Dystore\Api\Base\Contracts\Extendable;
use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 * @template TValue of \Dystore\Api\Base\Repositories\Storage
 *
 * @extends \Illuminate\Support\Collection<TKey, TValue>
 */
abstract class Repository extends Collection
{
    public function resolve(?Extendable $extendable = null): iterable
    {
        return $this
            ->map(function (mixed $value) use ($extendable) {
                if ($value instanceof Closure) {
                    $value = Closure::bind($value, $extendable, get_parent_class($extendable));

                    $value = $value($extendable);
                }

                return $value;
            })
            ->toArray();
    }
}
