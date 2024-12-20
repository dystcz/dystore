<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can read thumbnail through relationship', function () {
    /** @var TestCase $this */
    $variants = ProductVariantFactory::new()
        ->for(Product::factory(), 'product')
        ->withThumbnail()
        ->create();

    $variant = $variants->first();

    $response = $this
        ->jsonApi()
        ->expects('media')
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/thumbnail"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($variant->thumbnail)
        ->assertDoesntHaveIncluded();
})->group('product_variants');
