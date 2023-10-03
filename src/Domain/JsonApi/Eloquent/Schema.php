<?php

namespace Dystcz\LunarApi\Domain\JsonApi\Eloquent;

use Dystcz\LunarApi\Domain\JsonApi\Contracts\Schema as SchemaContract;
use Dystcz\LunarApi\Domain\JsonApi\Extensions\Contracts\Extendable as ExtendableContract;
use Dystcz\LunarApi\Domain\JsonApi\Extensions\Contracts\SchemaExtension as SchemaExtensionContract;
use Dystcz\LunarApi\Domain\JsonApi\Extensions\Contracts\SchemaManifest as SchemaManifestContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Contracts\Server\Server;
use LaravelJsonApi\Core\Schema\IncludePathIterator;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema as BaseSchema;

abstract class Schema extends BaseSchema implements ExtendableContract, SchemaContract
{
    /**
     * The maximum depth of include paths.
     */
    protected int $maxDepth = 0;

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
     * Schema extension.
     */
    protected SchemaExtensionContract $extension;

    /**
     * Schema constructor.
     */
    public function __construct(
        Server $server,
    ) {
        $this->extension = App::make(SchemaManifestContract::class)::for(static::class);

        $this->server = $server;
    }

    /**
     * {@inheritDoc}
     */
    public static function resource(): string
    {
        return Config::get('lunar-api.domains.'.static::type().'resource', parent::resource());
    }

    /**
     * {@inheritDoc}
     */
    public static function authorizer(): string
    {
        return Config::get('lunar-api.domains.'.static::type().'authorizer', parent::authorizer());
    }

    /**
     * {@inheritDoc}
     */
    public function repository(): Repository
    {
        return new Repository(
            $this,
            $this->driver(),
            $this->parser(),
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
            Arr::wrap($this->extension->with()->resolve($this)),
        );

        return array_values(array_unique($paths));
    }

    /**
     * {@inheritDoc}
     */
    public function includePaths(): iterable
    {
        if (0 < $this->maxDepth) {
            return new IncludePathIterator(
                $this->server->schemas(),
                $this,
                $this->maxDepth
            );
        }

        return [
            ...$this->extension->includePaths()->resolve($this),

            ...parent::includePaths(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function fields(): iterable
    {
        return $this->extension->fields()->resolve($this);

    }

    /**
     * {@inheritDoc}
     */
    public function filters(): iterable
    {
        return [
            WhereIdIn::make($this),

            ...$this->extension->filters()->resolve($this),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function sortables(): iterable
    {
        return $this->extension->sortables()->resolve($this);
    }

    /**
     * {@inheritDoc}
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make()
            ->withDefaultPerPage(
                Config::get('lunar-api.pagination.per_page', 12)
            );
    }

    /**
     * Allow specific related resources to be accessed.
     */
    public function showRelated(): array
    {
        $relations = array_merge(
            Arr::wrap($this->showRelated),
            Arr::wrap($this->extension->showRelated()->resolve($this)),
        );

        return array_values(array_unique($relations));
    }

    /**
     * Allow specific relationships to be accessed.
     */
    public function showRelationship(): array
    {
        if (empty($this->showRelationship)) {
            return $this->showRelated();
        }

        $paths = array_merge(
            Arr::wrap($this->showRelationship),
            Arr::wrap($this->extension->showRelationship()->resolve($this)),
        );

        return array_values(array_unique($paths));
    }
}