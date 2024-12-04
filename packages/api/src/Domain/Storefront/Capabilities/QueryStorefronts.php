<?php

namespace Dystore\Api\Domain\Storefront\Capabilities;

use Dystore\Api\Domain\Storefront\Entities\StorefrontStorage;
use Illuminate\Support\Collection;
use LaravelJsonApi\Contracts\Store\HasSingularFilters;
use LaravelJsonApi\NonEloquent\Capabilities\QueryAll;

class QueryStorefronts extends QueryAll implements HasSingularFilters
{
    private StorefrontStorage $storage;

    /**
     * QuerySites constructor.
     */
    public function __construct(StorefrontStorage $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }

    public function get(): iterable
    {
        $storefronts = Collection::make([$this->storage->find()]);

        return $storefronts;
    }

    /**
     * {@inheritDoc}
     */
    public function firstOrMany()
    {
        // NOTE: Always return single storefront session
        return $this->storage->find();
    }

    /**
     * {@inheritDoc}
     */
    public function firstOrPaginate(?array $page)
    {
        if (empty($page)) {
            return $this->firstOrMany();
        }

        return $this->paginate($page);
    }
}
