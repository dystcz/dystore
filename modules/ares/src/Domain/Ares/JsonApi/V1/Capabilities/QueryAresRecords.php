<?php

namespace Modules\DystoreAres\Domain\Ares\JsonApi\V1\Capabilities;

use LaravelJsonApi\NonEloquent\Capabilities\QueryAll;
use Modules\DystoreAres\Domain\Ares\Entities\AresRecordStorage;

class QueryAresRecords extends QueryAll
{
    private readonly AresRecordStorage $records;

    public function __construct(AresRecordStorage $records)
    {
        parent::__construct();

        $this->records = $records;
    }

    /**
     * {@inheritDoc}
     */
    public function get(): iterable
    {
        return $this->records->all();
    }
}
