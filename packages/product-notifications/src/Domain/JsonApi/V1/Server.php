<?php

namespace Dystore\ProductNotifications\Domain\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Servers\Server as BaseServer;
use Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema;

class Server extends BaseServer
{
    /**
     * Get the server's list of schemas.
     */
    protected function allSchemas(): array
    {
        return [
            ProductNotificationSchema::class,
        ];
    }
}
