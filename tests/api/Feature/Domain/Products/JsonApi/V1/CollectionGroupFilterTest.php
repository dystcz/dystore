<?php

use Dystore\Api\Domain\Prices\Models\Price;
use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;

uses(TestCase::class, RefreshDatabase::class)
    ->group('products', 'filters');

it('can filter products by a single collection group', function () {
    /** @var TestCase $this */
    $group = CollectionGroup::factory()->create(['handle' => 'category']);

    $collectionA = Collection::factory()->create(['collection_group_id' => $group->id]);
    $collectionB = Collection::factory()->create(['collection_group_id' => $group->id]);

    $matchingProduct = Product::factory()
        ->has(ProductVariant::factory()->has(Price::factory()), 'variants')
        ->create();
    $matchingProduct->collections()->attach([$collectionA->id]);

    $nonMatchingProduct = Product::factory()
        ->has(ProductVariant::factory()->has(Price::factory()), 'variants')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl("/products?filter[collection_groups][category][id]={$collectionA->id}"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany([$matchingProduct])
        ->assertDoesntHaveIncluded();
});

it('can filter products by multiple collection ids within a group using OR logic', function () {
    /** @var TestCase $this */
    $group = CollectionGroup::factory()->create(['handle' => 'category']);

    $collectionA = Collection::factory()->create(['collection_group_id' => $group->id]);
    $collectionB = Collection::factory()->create(['collection_group_id' => $group->id]);
    $collectionC = Collection::factory()->create(['collection_group_id' => $group->id]);

    $productA = Product::factory()
        ->has(ProductVariant::factory()->has(Price::factory()), 'variants')
        ->create();
    $productA->collections()->attach([$collectionA->id]);

    $productB = Product::factory()
        ->has(ProductVariant::factory()->has(Price::factory()), 'variants')
        ->create();
    $productB->collections()->attach([$collectionB->id]);

    $productC = Product::factory()
        ->has(ProductVariant::factory()->has(Price::factory()), 'variants')
        ->create();
    $productC->collections()->attach([$collectionC->id]);

    $ids = implode(',', [$collectionA->id, $collectionB->id]);

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl("/products?filter[collection_groups][category][id]={$ids}"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany([$productA, $productB])
        ->assertDoesntHaveIncluded();
});

it('can filter products across multiple collection groups with AND logic', function () {
    /** @var TestCase $this */
    $groupCategory = CollectionGroup::factory()->create(['handle' => 'category']);
    $groupSeason = CollectionGroup::factory()->create(['handle' => 'season']);

    $categoryA = Collection::factory()->create(['collection_group_id' => $groupCategory->id]);
    $seasonWinter = Collection::factory()->create(['collection_group_id' => $groupSeason->id]);
    $seasonSummer = Collection::factory()->create(['collection_group_id' => $groupSeason->id]);

    // Product in categoryA AND seasonWinter — should match
    $matchingProduct = Product::factory()
        ->has(ProductVariant::factory()->has(Price::factory()), 'variants')
        ->create();
    $matchingProduct->collections()->attach([$categoryA->id, $seasonWinter->id]);

    // Product in categoryA but NOT in seasonWinter — should NOT match
    $categoryOnlyProduct = Product::factory()
        ->has(ProductVariant::factory()->has(Price::factory()), 'variants')
        ->create();
    $categoryOnlyProduct->collections()->attach([$categoryA->id, $seasonSummer->id]);

    // Product in seasonWinter but NOT in categoryA — should NOT match
    $seasonOnlyProduct = Product::factory()
        ->has(ProductVariant::factory()->has(Price::factory()), 'variants')
        ->create();
    $seasonOnlyProduct->collections()->attach([$seasonWinter->id]);

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl("/products?filter[collection_groups][category][id]={$categoryA->id}&filter[collection_groups][season][id]={$seasonWinter->id}"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany([$matchingProduct])
        ->assertDoesntHaveIncluded();
});

it('returns all products when collection_groups filter value is empty', function () {
    /** @var TestCase $this */
    $products = Product::factory()
        ->has(ProductVariant::factory()->has(Price::factory()), 'variants')
        ->count(2)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl('/products'));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($products);
});
