<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    generateUrls();
});

it('can read default url through relationship', function () {
    /** @var TestCase $this */
    $variants = ProductVariantFactory::new()
        ->for(Product::factory(), 'product')
        ->create();

    $variant = $variants->first();

    $response = $this
        ->jsonApi()
        ->expects('urls')
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/default_url"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($variant->defaultUrl)
        ->assertDoesntHaveIncluded();
})->group('product_variants');
