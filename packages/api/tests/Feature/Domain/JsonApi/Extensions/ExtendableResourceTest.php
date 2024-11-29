<?php

use Dystore\Api\Base\Manifests\ResourceManifest;
use Dystore\Api\Domain\JsonApi\Resources\JsonApiResource;
use Dystore\Api\Domain\JsonApi\V1\Server;
use Dystore\Api\Domain\Products\Factories\ProductFactory;
use Dystore\Api\Tests\Feature\Domain\JsonApi\Extensions\ProductResourceMock;
use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use LaravelJsonApi\Core\Resources\Relation;

uses(TestCase::class, RefreshDatabase::class);

test('a resource attributes can be extended', function () {
    ResourceManifest::extend(ProductResourceMock::class)
        ->setAttributes(fn (JsonApiResource $resource) => [
            'secret_id' => $resource->resource->id,
            'nazdar' => 'cau',
            'ahoj' => 'zdar',
        ]);

    expect(ResourceManifest::extend(ProductResourceMock::class)->attributes()->all()[0])
        ->value()
        ->toBeInstanceOf(Closure::class);

    $server = App::make(Server::class, ['name' => 'v1']);

    $productSchemaInstance = $server->schemas()->schemaFor('products');

    $productResourceInstance = new ProductResourceMock(
        $productSchemaInstance,
        $product = ProductFactory::new()->create()
    );

    expect(iterator_to_array($productResourceInstance->attributes(null)))
        ->toMatchArray([
            'secret_id' => $product->id,
            'nazdar' => 'cau',
            'ahoj' => 'zdar',
        ]);
});

test('a resource relationships can be extended', function () {
    ResourceManifest::extend(ProductResourceMock::class)
        ->setRelationships(fn (JsonApiResource $resource) => [
            $resource->relation('golden_chocolate'),
        ]);

    expect(ResourceManifest::extend(ProductResourceMock::class)->relationships()->all()[0])
        ->value()
        ->toBeInstanceOf(Closure::class);

    $server = App::make(Server::class, ['name' => 'v1']);

    $productSchemaInstance = $server->schemas()->schemaFor('products');
    $productResourceInstance = new ProductResourceMock(
        $productSchemaInstance,
        ProductFactory::new()->create()
    );

    expect(iterator_to_array($productResourceInstance->relationships(null))['golden_chocolate'])
        ->toBeInstanceOf(Relation::class);
});
