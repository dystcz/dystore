<?php

namespace Dystore\Stripe\Tests\Stubs\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Servers\Server as BaseServer;
use Dystore\Stripe\Tests\Stubs\Users\JsonApi\V1\UserSchema;

class Server extends BaseServer
{
    /**
     * Get the server's list of schemas.
     */
    protected function allSchemas(): array
    {
        return [
            UserSchema::class,
        ];
    }
}
