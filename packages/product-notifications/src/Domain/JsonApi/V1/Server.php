<?php

namespace Dystcz\LunarProductNotification\Domain\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Servers\Server as BaseServer;
use Dystcz\LunarProductNotification\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema;

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
