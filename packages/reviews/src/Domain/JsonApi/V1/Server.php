<?php

namespace Dystcz\LunarApiReviews\Domain\JsonApi\V1;

use Dystcz\LunarApi\Domain\JsonApi\Servers\Server as BaseServer;
use Dystcz\LunarApiReviews\Domain\Reviews\JsonApi\V1\ReviewSchema;

class Server extends BaseServer
{
    /**
     * Get the server's list of schemas.
     */
    protected function allSchemas(): array
    {
        return [
            ReviewSchema::class,
        ];
    }
}
