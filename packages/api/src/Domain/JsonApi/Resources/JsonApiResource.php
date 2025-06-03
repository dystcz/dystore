<?php

namespace Dystore\Api\Domain\JsonApi\Resources;

use Closure;
use Dystore\Api\Base\Contracts\Extendable as ExtendableContract;
use Dystore\Api\Base\Facades\JsonApiManifest;
use Dystore\Api\Domain\JsonApi\Contracts\JsonApiResource as ResourceContract;
use Dystore\Api\Domain\JsonApi\Eloquent\Fields\AttributeData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use LaravelJsonApi\Contracts\Resources\Serializer\Attribute as SerializableAttribute;
use LaravelJsonApi\Contracts\Resources\Serializer\Relation as SerializableRelation;
use LaravelJsonApi\Contracts\Schema\Schema;
use LaravelJsonApi\Core\Resources\JsonApiResource as BaseApiResource;
use LaravelJsonApi\Core\Resources\Relation;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class JsonApiResource extends BaseApiResource implements ExtendableContract, ResourceContract
{
    /**
     * JsonApiResource constructor.
     */
    public function __construct(
        protected Schema $schema,
        public object $resource,
    ) {
        parent::__construct($schema, $resource);
    }

    /**
     * Get the resource's attributes.
     *
     * @param  Request|null  $request
     */
    public function attributes($request): iterable
    {
        /** @var Model $model */
        $model = $this->resource;

        foreach ($this->allAttributes($request) as $key => $attr) {
            if ($attr instanceof AttributeData && $request?->has('attribute_data')) {
                $attr = $attr->serializeUsing(
                    static fn ($value) => $value->only(explode(',', $request->get('attribute_data')))
                );
            }

            if ($attr instanceof AttributeData && $attr->flatten()) {
                foreach ($attr->serialize($this->resource) as $key => $attr) {
                    yield $key => $attr;
                }
            }

            if ($attr instanceof SerializableAttribute && $attr->isNotHidden($request)) {
                yield $attr->serializedFieldName() => $attr->serialize($this->resource);
            }

            if (is_scalar($attr) && is_scalar($key)) {
                yield $key => $attr;
            }
        }
    }

    /**
     * Get the resource's relationships.
     *
     * @param  Request|null  $request
     */
    public function relationships($request): iterable
    {
        foreach ($this->allRelationships($request) as $relation) {
            if ($relation instanceof Relation && ! $relation instanceof SerializableRelation) {
                yield $relation->fieldName() => $relation;
            }

            if ($relation instanceof SerializableRelation && $relation->isNotHidden($request)) {
                yield $relation->serializedFieldName() => $this->serializeRelation($relation);
            }
        }
    }

    /**
     * Get all resource's attributes.
     *
     * @param  Request|null  $request
     * @return array<int,mixed>
     */
    protected function allAttributes($request): iterable
    {
        return [
            ...$this->schema->attributes(),
            ...JsonApiManifest::resource(static::class)->attributes()->all(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultAttributes(): array
    {
        return [];
    }

    /**
     * Get all resource's relationships.
     *
     * @param  Request|null  $request
     * @return array<int,mixed>
     */
    protected function allRelationships($request): iterable
    {
        return [
            ...$this->schema->relationships(),
            ...JsonApiManifest::resource(static::class)->relationships()->all(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultRelationships(): array
    {
        return [];
    }

    /**
     * Get extended resource's relationships.
     *
     * @param  array<int,mixed>  $fields
     */
    protected function extendedFields(array $fields): array
    {
        $fields = array_map(function ($field) {
            $field = $field->value();

            if ($field instanceof Closure) {
                $field = Closure::bind($field, $this, parent::class);

                return $field($this);
            }

            return $field;
        }, $fields);

        $recursiveArrayIterator = new RecursiveArrayIterator($fields, RecursiveArrayIterator::CHILD_ARRAYS_ONLY);
        $iterator = new RecursiveIteratorIterator($recursiveArrayIterator);

        return iterator_to_array($iterator);
    }
}
