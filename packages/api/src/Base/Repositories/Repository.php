<?php

namespace Dystore\Api\Base\Repositories;

use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 * @template TValue of \Dystore\Api\Base\Repositories\Storage
 *
 * @extends \Illuminate\Support\Collection<TKey, TValue>
 */
abstract class Repository extends Collection {}
