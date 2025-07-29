<?php

use Dystore\Api\Domain\Prices\Models\Price;
use Dystore\Api\Domain\ProductAssociations\Models\ProductAssociation;
use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('product_associations');

it('can list inverse product associations through relationship', function () {
    /** @var TestCase $this */

    /** @var Product $productA */
    $productA = Product::factory()->create();

    /** @var Product $productB */
    $productB = Product::factory()
        ->has(ProductVariantFactory::new()->has(Price::factory()), 'variants')
        ->create();

    $productA->associate(
        $productB,
        ProductAssociation::CROSS_SELL
    );

    $response = $this
        ->jsonApi()
        ->expects('product_associations')
        ->get(serverUrl("/products/{$productB->getRouteKey()}/inverse_product_associations"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($productB->inverseAssociations)
        ->assertDoesntHaveIncluded();
});

it('cannot list unpublished product associations through relationship', function () {
    /** @var TestCase $this */

    /** @var Product $productA */
    $productA = Product::factory()->create();

    /** @var Product $productB */
    $productB = Product::factory()
        ->has(ProductVariantFactory::new()->has(Price::factory()), 'variants')
        ->create([
            'status' => 'draft',
        ]);

    $productA->associate(
        $productB,
        ProductAssociation::CROSS_SELL
    );

    $response = $this
        ->jsonApi()
        ->expects('product_associations')
        ->get(serverUrl("/products/{$productB->getRouteKey()}/inverse_product_associations"));

    $response
        ->assertSuccessful()
        ->assertFetchedNone()
        ->assertDoesntHaveIncluded();
});

it('can count inverse product associations', function () {
    /** @var TestCase $this */

    /** @var Product $productA */
    $productA = Product::factory()->create();

    /** @var Product $productB */
    $productB = Product::factory()
        ->has(ProductVariantFactory::new()->has(Price::factory()), 'variants')
        ->create();

    $productA->associate(
        $productB,
        ProductAssociation::CROSS_SELL
    );

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl("/products/{$productB->getRouteKey()}?with_count=inverse_product_associations"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($productB);

    expect($response->json('data.relationships.inverse_product_associations.meta.count'))->toBe(1);
})->group('product_associations', 'counts');
