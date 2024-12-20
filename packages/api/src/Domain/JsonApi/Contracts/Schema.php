<?php

namespace Dystore\Api\Domain\JsonApi\Contracts;

use LaravelJsonApi\Contracts\Schema\Schema as BaseSchemaContract;

interface Schema extends BaseSchemaContract
{
    /**
     * Allow specific related resources to be accessed.
     */
    public function showRelated(): array;

    /**
     * Allow specific relationships to be accessed.
     */
    public function showRelationship(): array;
}
