<?php

namespace Modules\DystoreAres\Domain\Ares\JsonApi\V1;

use LaravelJsonApi\Contracts\Store\QueriesAll;
use LaravelJsonApi\NonEloquent\AbstractRepository;
use Modules\DystoreAres\Domain\Ares\Entities\AresRecordStorage;
use Modules\DystoreAres\Domain\Ares\JsonApi\V1\Capabilities\QueryAresRecords;

class AresRecordRepository extends AbstractRepository implements QueriesAll
{
    private readonly AresRecordStorage $storage;

    public function __construct(AresRecordStorage $storage)
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
    public function queryAll(): QueryAresRecords
    {
        return QueryAresRecords::make()
            ->withServer($this->server)
            ->withSchema($this->schema);
    }
}
