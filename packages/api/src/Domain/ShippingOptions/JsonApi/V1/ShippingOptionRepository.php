<?php

namespace Dystore\Api\Domain\ShippingOptions\JsonApi\V1;

use Dystore\Api\Domain\ShippingOptions\Entities\ShippingOptionStorage;
use Dystore\Api\Domain\ShippingOptions\JsonApi\V1\Capabilities\QueryShippingOptions;
use LaravelJsonApi\Contracts\Store\QueriesAll;
use LaravelJsonApi\NonEloquent\AbstractRepository;

class ShippingOptionRepository extends AbstractRepository implements QueriesAll
{
    private readonly ShippingOptionStorage $storage;

    public function __construct(ShippingOptionStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritDoc}
     */
    public function find(string $resourceId): ?object
    {
        return $this->storage->find($resourceId);
    }

    /**
     * {@inheritDoc}
     */
    public function queryAll(): QueryShippingOptions
    {
        return QueryShippingOptions::make()
            ->withServer($this->server)
            ->withSchema($this->schema);
    }
}
