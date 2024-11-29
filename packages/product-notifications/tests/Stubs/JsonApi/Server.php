<?php

namespace Dystcz\LunarProductNotification\Tests\Stubs\JsonApi;

use Dystcz\LunarProductNotification\Domain\JsonApi\V1\Server as BaseServer;
use Dystcz\LunarProductNotification\Domain\ProductNotifications\JsonApi\V1\ProductNotificationSchema;
use Dystcz\LunarProductNotification\Tests\Stubs\ProductVariants\ProductVariantSchema;
use Dystcz\LunarProductNotification\Tests\Stubs\Users\UserSchema;

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
