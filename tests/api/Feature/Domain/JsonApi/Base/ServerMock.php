<?php

namespace Dystore\Tests\Api\Feature\Domain\JsonApi\Base;

use Dystore\Tests\Api\Feature\Domain\JsonApi\Stubs\BaseSchemaMock;
use Dystore\Tests\Api\Feature\Domain\JsonApi\Stubs\EloquentSchemaMock;
use LaravelJsonApi\Core\Server\Server;

class ServerMock extends Server
{
    public function allSchemas(): array
    {
        return [
            EloquentSchemaMock::class,
            // BaseSchemaMock::class,
        ];
    }
}
