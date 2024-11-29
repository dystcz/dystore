<?php

namespace Dystore\Api\Domain\JsonApi\Core\Schema;

use Dystore\Api\Support\Models\Actions\SchemaType;

final class TypeResolver
{
    private static array $cache = [];

    /**
     * Manually register the resource type to use for a schema class.
     */
    public static function register(string $schemaClass, string $resourceType): void
    {
        self::$cache[$schemaClass] = $resourceType;
    }

    /**
     * Resolve the JSON:API resource type from the fully-qualified schema class.
     *
     * @param  class-string  $schemaClass
     */
    public function __invoke(string $schemaClass): string
    {
        if (isset(self::$cache[$schemaClass])) {
            return self::$cache[$schemaClass];
        }

        return SchemaType::get($schemaClass::model());
    }
}
