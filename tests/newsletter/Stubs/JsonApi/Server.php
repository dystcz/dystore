<?php

namespace Dystore\Tests\Newsletter\Stubs\JsonApi;

use Dystore\Api\Domain\JsonApi\V1\Server as BaseServer;
use Dystore\Newsletter\Domain\Newsletter\JsonApi\V1\NewsletterSchema;

class Server extends BaseServer
{
    /**
     * Get the server's list of schemas.
     */
    protected function allSchemas(): array
    {
        return [
            NewsletterSchema::class,
        ];
    }
}
