<?php

namespace Dystore\Api\Domain\JsonApi\Contracts;

interface JsonApiResource
{
    /**
     * Get default attributes for the resource.
     */
    public static function defaultAttributes(): array;

    /**
     * Get default relationships for the resource.
     */
    public static function defaultRelationships(): array;
}
