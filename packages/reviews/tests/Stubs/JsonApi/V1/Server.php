<?php

namespace Dystcz\LunarApiReviews\Tests\Stubs\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Servers\Server as BaseServer;
use Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewSchema;
use Dystcz\LunarApiReviews\Tests\Stubs\ProductVariants\ProductVariantSchema;
use Dystcz\LunarApiReviews\Tests\Stubs\Users\UserSchema;

class Server extends BaseServer
{
    /**
     * Get the server's list of schemas.
     */
    protected function allSchemas(): array
    {
        return [
            ReviewSchema::class,
            ProductVariantSchema::class,
            UserSchema::class,
        ];
    }
}
