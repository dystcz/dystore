<?php

use Carbon\Carbon;
use Dystore\Api\Domain\CustomerGroups\Models\CustomerGroup;
use Dystore\Api\Domain\Prices\Models\Price;
use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('products');

it('can list bare products', function () {
    /** @var TestCase $this */
    $products = Product::factory()
        ->has(
            ProductVariant::factory()->has(Price::factory())->count(2),
            'variants'
        )
        ->count(3)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl('/products'));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($products)
        ->assertDoesntHaveIncluded();
});

it('can list only products in correct customer group', function () {
    /** @var TestCase $this */
    $defaultCustomerGroup = CustomerGroup::getDefault();

    $exclusiveCustomerGroup = CustomerGroup::factory()->create([
        'name' => 'Super ultra exclusive club',
        'handle' => 'swag',
    ]);

    $products = Product::factory()
        ->has(
            ProductVariant::factory()->has(Price::factory())->count(2),
            'variants'
        )
        ->count(3)
        ->create();

    $exclusiveProducts = Product::factory()
        ->has(
            ProductVariant::factory()->has(Price::factory())->count(2),
            'variants'
        )
        ->count(3)
        ->create();

    foreach ($exclusiveProducts as $product) {
        /** @var Product $product */
        $product->unscheduleCustomerGroup($defaultCustomerGroup, ['visible' => false]);
        $product->scheduleCustomerGroup($exclusiveCustomerGroup, Carbon::now());
    }

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl('/products'));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($products)
        ->assertDoesntHaveIncluded();
});

it('cannot list unpublished products', function () {
    /** @var TestCase $this */
    $products = Product::factory()
        ->has(
            ProductVariant::factory()->has(Price::factory())->count(2),
            'variants'
        )
        ->count(3)
        ->create(['status' => 'draft']);

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl('/products'));

    $response
        ->assertSuccessful()
        ->assertFetchedNone()
        ->assertDoesntHaveIncluded();
})->group('products', 'policies');
