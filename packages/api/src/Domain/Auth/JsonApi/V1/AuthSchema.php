<?php

namespace Dystore\Api\Domain\Auth\JsonApi\V1;

use Dystore\Api\Domain\Auth\JsonApi\Proxies\AuthUser;
use Dystore\Api\Domain\JsonApi\Eloquent\ProxySchema;
use Dystore\Api\Domain\Users\JsonApi\V1\UserSchema;

class AuthSchema extends ProxySchema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = AuthUser::class;

    /**
     * {@inheritDoc}
     */
    public static function type(): string
    {
        return 'auth';
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [
            ...UserSchema::defaultFields(),
        ];
    }

    /**
     * Determine if the resource is authorizable.
     */
    public function authorizable(): bool
    {
        return false;
    }
}
