<?php

namespace Dystore\Tests\ProductNotifications\Stubs\JsonApi;

use Dystore\ProductNotifications\Domain\JsonApi\V1\Server as BaseServer;
use Dystore\ProductNotifications\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema;
use Dystore\Tests\ProductNotifications\Stubs\ProductVariants\ProductVariantSchema;
use Dystore\Tests\ProductNotifications\Stubs\Users\UserSchema;

class Server extends BaseServer
{
    /**
     * Get the server's list of schemas.
     */
    protected function allSchemas(): array
    {
        return [
            UserSchema::class,
            ProductVariantSchema::class,
            ProductNotificationSchema::class,
        ];
    }
}
