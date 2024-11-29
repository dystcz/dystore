
<?php

use Dystcz\LunarApi\Domain\Media\Factories\MediaFactory;
use Dystcz\LunarApi\Domain\Prices\Factories\PriceFactory;
use Dystcz\LunarApi\Domain\Prices\Models\Price;
use Dystcz\LunarApi\Domain\Products\Models\Product;
use Dystcz\LunarApi\Domain\ProductVariants\Models\ProductVariant;
use Dystcz\LunarApi\Domain\Tags\Models\Tag;
use Dystcz\LunarApi\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can show product detail', function () {
    /** @var TestCase $this */
    $product = Product::factory()->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl('/products/'.$product->getRouteKey()));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product)
        ->assertDoesntHaveIncluded();
})->group('products');

it('returns error response when product doesnt exists', function () {
    /** @var TestCase $this */
    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl('/products/1'));

    $response
        ->assertErrorStatus([
            'status' => '404',
            'title' => 'Not Found',
        ]);
})->group('products', 'policies');

test('can show a product with included images', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(MediaFactory::new(), 'images')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->includePaths('images')
        ->get(serverUrl('/products/'.$product->getRouteKey()));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product)
        ->assertIsIncluded('media', $product->images->first());
})->group('products');

it('can show a product with included thumbnail', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(MediaFactory::new()->thumbnail(), 'images')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->includePaths('thumbnail')
        ->get(serverUrl('/products/'.$product->getRouteKey()));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product)
        ->assertIsIncluded('media', $product->thumbnail);
})->group('products');

it('can show a product with included variants', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(
            ProductVariant::factory()->has(PriceFactory::new())->count(3),
            'variants'
        )
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->includePaths('variants')
        ->get(serverUrl('/products/'.$product->getRouteKey()));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product)
        ->assertIsIncluded('variants', $product->variants->first());
})->group('products');

it('can show a product with variants count', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(
            ProductVariant::factory()->has(Price::factory())->count(5),
            'variants'
        )
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get('/api/v1/products/'.$product->getRouteKey().'?withCount=variants');

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product);

    expect($response->json('data.relationships.variants.meta.count'))->toBe(5);
})->group('products');

it('can show a product with included tags', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(Tag::factory()->count(2))
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->includePaths('tags')
        ->get('/api/v1/products/'.$product->getRouteKey());

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product)
        ->assertIncluded([
            ['type' => 'tags', 'id' => $product->tags[0]->getRouteKey()],
            ['type' => 'tags', 'id' => $product->tags[1]->getRouteKey()],
        ]);
})->group('products');
