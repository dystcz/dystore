<?php

namespace Dystore\Api\Domain\JsonApi\Eloquent;

use Dystore\Api\Base\Contracts\Extendable as ExtendableContract;
use Dystore\Api\Base\Facades\JsonApiManifest;
use Dystore\Api\Domain\JsonApi\Contracts\Schema as SchemaContract;
use Dystore\Api\Domain\JsonApi\Core\Schema\TypeResolver;
use Dystore\Api\Facades\Api;
use Dystore\Api\Support\Models\Actions\ModelKey;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use LaravelJsonApi\Core\Schema\IncludePathIterator;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Filters\WhereIdNotIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema as BaseSchema;
use LaravelJsonApi\HashIds\HashId;
use LogicException;
use Lunar\Facades\ModelManifest;

abstract class Schema extends BaseSchema implements ExtendableContract, SchemaContract
{
    /**
     * {@inheritDoc}
     */
    public static string $model;

    /**
     * The maximum depth of include paths.
     */
    protected int $maxDepth = 5;

    /**
     * The default paging parameters to use if the client supplies none.
     */
    protected ?array $defaultPagination = ['number' => 1];

    /**
     * Allow viewing of related resources.
     *
     * @property string[] $showRelated
     */
    protected array $showRelated = [];

    /**
     * Allow viewing of relationships.
     *
     * @property string[] $showRelationship
     */
    protected array $showRelationship = [];

    /**
     * {@inheritDoc}
     */
    public static function type(): string
    {
        $resolver = new TypeResolver;

        return $resolver(static::class);
    }

    /**
     * {@inheritDoc}
     */
    public static function model(): string
    {
        if (! isset(static::$model)) {
            throw new LogicException('The model class name must be set.');
        }

        if (class_exists(static::$model)) {
            return static::$model;
        }

        if (App::isBooted() && $model = ModelManifest::get(static::$model)) {
            return $model;
        }

        return static::$model;
    }

    /**
     * {@inheritDoc}
     */
    public static function resource(): string
    {
        $type = Str::snake(static::type());

        return Config::get(
            "dystore.domains.{$type}.resource",
            parent::resource(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public static function authorizer(): string
    {
        $type = Str::snake(static::type());

        return Config::get(
            "dystore.domains.{$type}.authorizer",
            parent::authorizer(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function uriType(): string
    {
        if ($this->uriType) {
            return $this->uriType;
        }

        return $this->uriType = $this->type();
    }

    /**
     * {@inheritDoc}
     */
    public function repository(): Repository
    {
        return new Repository(
            schema: $this,
            driver: $this->driver(),
            parser: $this->parser(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function with(): array
    {
        $paths = array_merge(
            parent::with(),
            Arr::wrap($this->with),
            Arr::wrap(JsonApiManifest::schema(static::class)->with()->resolve($this)),
        );

        return array_values(array_unique($paths));
    }

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
    public function includePaths(): iterable
    {
        $includePaths = JsonApiManifest::schema(static::class)->includePaths()->resolve($this);

        if ($this->maxDepth > 0) {
            return [
                ...$includePaths,
                ...new IncludePathIterator($this->server->schemas(), $this, $this->maxDepth),
            ];
        }

        return [
            ...$includePaths,
            ...parent::includePaths(),
        ];
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
        yield from JsonApiManifest::schema(static::class)->fields()->resolve($this);
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFields(): iterable
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
    public static function defaultSparseFields(): iterable
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function filters(): iterable
    {
        return [
            WhereIdIn::make($this)->delimiter(','),
            WhereIdNotIn::make($this, 'except')->delimiter(','),

            ...JsonApiManifest::schema(static::class)->filters()->resolve($this),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultFilters(): iterable
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function sortables(): iterable
    {
        yield from JsonApiManifest::schema(static::class)->sortables()->resolve($this);
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultSortables(): iterable
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make()
            ->withDefaultPerPage(
                Config::get('dystore.general.pagination.per_page', 24)
            );
    }

    /**
     * Allow specific related resources to be accessed.
     */
    public function showRelated(): array
    {
        $relations = array_merge(
            Arr::wrap($this->showRelated),
            JsonApiManifest::schema(static::class)->showRelated()->resolve($this)->toArray()
        );

        return array_values(array_unique($relations));
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultShowRelated(): array
    {
        return [];
    }

    /**
     * Allow specific relationships to be accessed.
     */
    public function showRelationships(): array
    {
        if (empty($this->showRelationship)) {
            return $this->showRelated();
        }

        $paths = array_merge(
            Arr::wrap($this->showRelationship),
            JsonApiManifest::schema(static::class)->showRelationships()->resolve($this)->toArray(),
        );

        return array_values(array_unique($paths));
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultShowRelationships(): array
    {
        return [];
    }

    /**
     * Get id or hashid field based on configuration.
     */
    public static function idField(?string $column = null): ID // ID|HashId
    {
        if (Api::usesHashids()) {
            return HashId::make($column)
                ->useConnection(ModelKey::get(static::model()))
                ->alreadyHashed();
        }

        return ID::make($column);
    }

    /**
     * Get the merge key for include paths.
     */
    private function getMergeKey(string $type): string
    {
        $types = [static::type(), $type];

        return implode('.', $types);
    }
}
