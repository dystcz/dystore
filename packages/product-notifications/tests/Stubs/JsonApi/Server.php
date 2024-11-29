<?php

namespace Dystcz\LunarApiProductNotification\Tests\Stubs\JsonApi;

use Dystcz\LunarApiProductNotification\Domain\JsonApi\V1\Server as BaseServer;
use Dystcz\LunarApiProductNotification\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema;
use Dystcz\LunarApiProductNotification\Tests\Stubs\ProductVariants\ProductVariantSchema;
use Dystcz\LunarApiProductNotification\Tests\Stubs\Users\UserSchema;

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
