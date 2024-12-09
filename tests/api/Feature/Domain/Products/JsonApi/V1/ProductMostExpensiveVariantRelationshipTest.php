<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Price;

uses(TestCase::class, RefreshDatabase::class)
    ->group('products', 'prices');

it('can read most expensive variant through relationship', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(ProductVariantFactory::new()->withPrice()->count(4), 'variants')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('product_variants')
        ->get(serverUrl("/products/{$product->getRouteKey()}/most_expensive_product_variant"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne(
            $product->variants->sortBy(
                fn ($variant) => $variant->prices->sortByDesc('price')->first()->price,
            )->first(),
        )
        ->assertDoesntHaveIncluded();
});

it('can read correct most expensive variant through relationship when customer group is set', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(ProductVariantFactory::new()->withPrice()->count(4), 'variants')
        ->create();

    /** @var CustomerGroup $customerGroup */
    $customerGroup = CustomerGroup::factory()->create();
    /** @var Currency $currency */
    $currency = Currency::getDefault();

    $mostExpensiveVariant = $product->variants->sortBy(
        fn ($variant) => $variant->prices->sortByDesc('price')->first()->price,
    )->first();

    $highestPrice = $mostExpensiveVariant->prices->first()->price;

    $otherVariant = $product->variants->where('id', '!=', $mostExpensiveVariant->id)->first();

    App::make(StorefrontSessionInterface::class)->setCustomerGroups(Collection::make([$customerGroup]));

    Price::factory()
        ->for($otherVariant, 'priceable')
        ->create([
            'price' => $highestPrice->value * 2,
            'customer_group_id' => $customerGroup->getKey(),
            'currency_id' => $currency->getKey(),
        ]);

    $response = $this
        ->jsonApi()
        ->expects('product_variants')
        ->get(serverUrl("/products/{$product->getRouteKey()}/most_expensive_product_variant"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($otherVariant)
        ->assertDoesntHaveIncluded();
});

afterEach(function () {
    App::make(StorefrontSessionInterface::class)->forget();
});
