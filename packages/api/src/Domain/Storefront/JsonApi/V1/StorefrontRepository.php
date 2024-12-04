<?php

namespace Dystore\Api\Domain\Storefront\JsonApi\V1;

use Dystore\Api\Domain\Storefront\Capabilities\CrudStorefront;
use Dystore\Api\Domain\Storefront\Capabilities\CrudStorefrontRelations;
use Dystore\Api\Domain\Storefront\Capabilities\QueryStorefronts;
use Dystore\Api\Domain\Storefront\Entities\StorefrontStorage;
use LaravelJsonApi\Contracts\Store\ModifiesToMany;
use LaravelJsonApi\Contracts\Store\ModifiesToOne;
use LaravelJsonApi\Contracts\Store\QueriesAll;
use LaravelJsonApi\NonEloquent\AbstractRepository;
use LaravelJsonApi\NonEloquent\Concerns\HasCrudCapability;
use LaravelJsonApi\NonEloquent\Concerns\HasRelationsCapability;

class StorefrontRepository extends AbstractRepository implements ModifiesToMany, ModifiesToOne, QueriesAll
{
    use HasCrudCapability;
    use HasRelationsCapability;

    private readonly StorefrontStorage $storage;

    public function __construct(StorefrontStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritDoc}
     */
    public function find(string $id): ?object
    {
        return $this->storage->find();
    }

    /**
     * {@inheritDoc}
     */
    public function queryAll(): QueryStorefronts
    {
        return QueryStorefronts::make()
            ->withServer($this->server())
            ->withSchema($this->schema());
    }

    /**
     * {@inheritDoc}
     */
    protected function crud(): CrudStorefront
    {
        return CrudStorefront::make();
    }

    /**
     * {@inheritDoc}
     */
    protected function relations(): CrudStorefrontRelations
    {
        return CrudStorefrontRelations::make();
    }
}
