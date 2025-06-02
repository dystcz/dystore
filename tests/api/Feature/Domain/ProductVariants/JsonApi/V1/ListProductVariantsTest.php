<?php

use Dystore\Api\Domain\Prices\Models\Price;
use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('product_variants');

it('can list bare product variants', function () {
    /** @var TestCase $this */
    $variants = ProductVariant::factory()
        ->count(2)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('product_variants')
        ->get(serverUrl('/product_variants'));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($variants)
        ->assertDoesntHaveIncluded();
});

it('cannot list variants of unpublished products', function () {
    /** @var TestCase $this */
    $variants = ProductVariant::factory()
        ->for(Product::factory()->create(['status' => 'draft']))
        ->has(Price::factory())
        ->count(2)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('product_variants')
        ->get(serverUrl('/product_variants'));

    $response
        ->assertSuccessful()
        ->assertFetchedNone()
        ->assertDoesntHaveIncluded();
});
