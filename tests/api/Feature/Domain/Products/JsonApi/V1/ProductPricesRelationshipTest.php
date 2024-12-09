<?php

use Dystore\Api\Domain\Products\Factories\ProductFactory;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Models\CustomerGroup;

uses(TestCase::class, RefreshDatabase::class)
    ->group('products', 'prices');

it('can list product prices through relationship', function () {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/prices"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($product->prices)
        ->assertDoesntHaveIncluded();
});

it('lists only base product prices through relationship when user is not logged in', function () {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $customerGroup = CustomerGroup::factory()->create();

    $lowestPrice = $product->prices->sortBy('price')->first();

    $lowestPrice->update([
        'customer_group_id' => $customerGroup->getKey(),
    ]);

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/prices"));

    $pricesWithoutCustomerGroup = $product->prices->filter(
        fn ($price) => $price->customer_group_id === null,
    );

    $response
        ->assertSuccessful()
        ->assertFetchedMany($pricesWithoutCustomerGroup)
        ->assertDoesntHaveIncluded();
});

it('lists correct product prices through relationship when customer group is set in storefront session', function () {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $user = User::factory()->create();

    $customerGroup = CustomerGroup::factory()->create();

    $lowestPrice = $product->prices->sortBy('price')->first();

    $lowestPrice->update([
        'customer_group_id' => $customerGroup->getKey(),
    ]);

    App::make(StorefrontSessionInterface::class)->setCustomerGroups(Collection::make([$customerGroup]));

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/prices"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($product->prices)
        ->assertDoesntHaveIncluded();
});

it('can count product prices', function () {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('products')
        ->get(serverUrl("/products/{$product->getRouteKey()}?with_count=prices"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product);

    expect($response->json('data.relationships.prices.meta.count'))->toBe(3);
});

afterEach(function () {
    App::make(StorefrontSessionInterface::class)->forget();
});
