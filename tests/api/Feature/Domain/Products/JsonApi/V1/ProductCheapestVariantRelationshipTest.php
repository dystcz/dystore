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
    ->group('products', 'product_variants');

it('can read cheapest variant through relationship', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(ProductVariantFactory::new()->withPrice()->count(4), 'variants')
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('product_variants')
        ->get(serverUrl("/products/{$product->getRouteKey()}/cheapest_product_variant"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne(
            $product->variants->sortBy(
                fn ($variant) => $variant->prices->sortBy('price')->first()->price,
            )->first(),
        )
        ->assertDoesntHaveIncluded();
});

it('can read correct cheapest variant through relationship when customer group is set', function () {
    /** @var TestCase $this */
    $product = Product::factory()
        ->has(ProductVariantFactory::new()->withPrice()->count(4), 'variants')
        ->create();

    /** @var CustomerGroup $customerGroup */
    $customerGroup = CustomerGroup::factory()->create();
    /** @var Currency $currency */
    $currency = Currency::getDefault();

    $cheapestVariant = $product->variants->sortBy(
        fn ($variant) => $variant->prices->sortBy('price')->first()->price,
    )->first();

    $lowestPrice = $cheapestVariant->prices->first()->price;

    $otherVariant = $product->variants->where('id', '!=', $cheapestVariant->id)->first();

    App::make(StorefrontSessionInterface::class)->setCustomerGroups(Collection::make([$customerGroup]));

    Price::factory()
        ->for($otherVariant, 'priceable')
        ->create([
            'price' => $lowestPrice->value / 2,
            'customer_group_id' => $customerGroup->getKey(),
            'currency_id' => $currency->getKey(),
        ]);

    $response = $this
        ->jsonApi()
        ->expects('product_variants')
        ->get(serverUrl("/products/{$product->getRouteKey()}/cheapest_product_variant"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($otherVariant)
        ->assertDoesntHaveIncluded();
});

afterEach(function () {
    App::make(StorefrontSessionInterface::class)->forget();
});
