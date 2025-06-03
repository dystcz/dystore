<?php

namespace Dystore\Api\Domain\JsonApi\Core\Schema;

use Dystore\Api\Base\Contracts\Extendable;
use Dystore\Api\Base\Facades\JsonApiManifest;
use Dystore\Api\Domain\JsonApi\Contracts\Schema as SchemaContract;
use LaravelJsonApi\Core\Schema\Schema as CoreSchema;

class Schema extends CoreSchema implements Extendable, SchemaContract
{
    /**
     * {@inheritDoc}
     */
    public static function defaultWith(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultIncludePaths(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function fields(): iterable
    {
        return JsonApiManifest::schema(static::class)->fields()->resolve($this);
    }

    // /**
    //  * {@inheritDoc}
    //  */
    // public function fields(): iterable
    // {
    //     $fields = iterator_to_array(JsonApiManifest::schema(static::class)->fields()->resolve($this));
    //
    //     foreach ($fields as $key => $field) {
    //         yield $field instanceof Closure ? $field($this) : $field;
    //     }
    // }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function sparseFields(): iterable
    {
        return JsonApiManifest::schema(static::class)->sparseFields()->resolve($this);
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultSparseFields(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function filters(): iterable
    {
        return JsonApiManifest::schema(static::class)->filters()->resolve($this);
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function sortables(): iterable
    {
        return JsonApiManifest::schema(static::class)->sortables()->resolve($this);
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultSortables(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function showRelated(): array
    {
        return JsonApiManifest::schema(static::class)->showRelated()->resolve($this);
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultShowRelated(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function showRelationships(): array
    {
        return JsonApiManifest::schema(static::class)->showRelationships()->resolve($this);
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultShowRelationships(): array
    {
        return [];
    }
}
