<?php

namespace Dystore\Tests\Api\Traits;

use Dystore\Tests\Api\Data\TestInclude;
use Dystore\Tests\Api\TestCase;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Core\Facades\JsonApi;
use LaravelJsonApi\Testing\TestResponse;

trait JsonApiTestHelpers
{
    /**
     * @param  class-string  $model
     * @param  array<string,mixed>  $data
     */
    public function createTest(
        string $schemaType,
        array $data,
    ): TestResponse {
        /** @var TestCase $this */
        $response = $this
            ->jsonApi()
            ->expects($schemaType)
            ->withData($data)
            ->post(serverUrl("/{$schemaType}"));

        return $response;
    }

    /**
     * @param  class-string  $model
     */
    public function indexTest(
        string $schemaType,
        string $model,
        int $modelCount = 5,
    ): TestResponse {

        $perPage = Config::get('dystore.general.pagination.per_page');

        /** @var TestCase $this */
        $models = $model::factory()
            ->count($modelCount)
            ->create();

        $response = $this
            ->jsonApi()
            ->expects($schemaType)
            ->get(serverUrl("/{$schemaType}"));

        $response
            ->assertSuccessful()
            ->assertFetchedMany(
                $models->when(
                    $modelCount > $perPage,
                    fn ($collection) => $collection->take($perPage),
                    fn ($collection) => $collection,
                ),
            );

        return $response;
    }

    /**
     * @param  class-string  $model
     * @param  array<string,mixed>  $data
     */
    public function updateTest(
        string $schemaType,
        Model|string $model,
        array $data,
    ): TestResponse {
        /** @var TestCase $this */
        $model instanceof Model ?: $model = $model::factory()->create();

        $response = $this
            ->jsonApi()
            ->expects($schemaType)
            ->withData($data)
            ->patch(serverUrl("/{$schemaType}/{$model->getRouteKey()}"));

        return $response;
    }

    /**
     * @param  class-string  $model
     */
    public function deleteTest(
        string $schemaType,
        string|Model $model,
    ): TestResponse {
        /** @var TestCase $this */
        $model instanceof Model ?: $model = $model::factory()->create();

        $response = $this
            ->jsonApi()
            ->expects($schemaType)
            ->delete(serverUrl("/{$schemaType}/{$model->getRouteKey()}"));

        return $response;
    }

    /**
     * @param  Collection<TestInclude>  $includes
     * @param  Factory<Model>  $factory
     */
    public function indexWithIncludesTest(
        Factory $factory,
        Collection $includes,
    ): TestResponse {

        $model = $factory->modelName();
        $schema = JsonApi::server('v1')->schemas()->schemaForModel($model);
        $schemaType = $schema->type();

        $perPage = Config::get('dystore.general.pagination.per_page');

        /** @var TestCase $this */
        $factory->create();

        $models = $model::query()
            ->with($includes->pluck('relation')->all())
            ->get();

        $response = $this
            ->jsonApi()
            ->expects($schemaType)
            ->includePaths(...$includes->keys())
            ->get(serverUrl("/{$schemaType}"));

        $included = Collection::make($includes)
            ->flatMap(function (TestInclude $include, string $includePath) use ($models) {
                return mapModelsToResponseData(
                    $include->type,
                    $include->getRelation($models),
                );
            });

        $response
            ->assertSuccessful()
            ->assertFetchedMany(
                $models->when(
                    $models->count() > $perPage,
                    fn ($collection) => $collection->take($perPage),
                    fn ($collection) => $collection,
                ),
            )
            ->assertIncluded($included);

        return $response;
    }

    /**
     * @param  class-string  $model
     */
    public function paginationTest(
        string $schemaType,
        string $model,
        int $modelCount = 20,
        int $page = 1,
        int $perPage = 24,
    ): TestResponse {
        /** @var TestCase $this */
        $models = $model::factory()
            ->count($modelCount)
            ->create();

        $response = $this
            ->jsonApi()
            ->expects($schemaType)
            ->page(['number' => $page, 'size' => $perPage])
            ->get(serverUrl("/{$schemaType}"));

        $this->assertDatabaseCount($models->first()->getTable(), $modelCount);

        $response
            ->assertSuccessful()
            ->assertFetchedMany($models->take($perPage));

        return $response;
    }
}
