<?php

use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Models\CustomerGroup;

uses(TestCase::class, RefreshDatabase::class)
    ->group('product_variants');

it('can read highest price through relationship', function () {
    /** @var TestCase $this */
    /** @var ProductVariant $variant */
    $variant = ProductVariantFactory::new()
        ->for(Product::factory(), 'product')
        ->withPrice()
        ->withPrice()
        ->withPrice()
        ->create();

    $variant = ProductVariant::query()
        ->with([
            'product',
            'prices.priceable',
            'prices.currency',
            'prices.customerGroup',
        ])
        ->findOrFail($variant->getKey());

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/highest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($variant->prices->sortByDesc('price')->first())
        ->assertDoesntHaveIncluded();
});

it('can read correct highest price when customer group is set', function () {
    /** @var TestCase $this */
    /** @var ProductVariant $variant */
    $variant = ProductVariantFactory::new()
        ->for(Product::factory(), 'product')
        ->withPrice()
        ->withPrice()
        ->withPrice()
        ->create();

    $variant = ProductVariant::query()
        ->with([
            'product',
            'prices.priceable',
            'prices.currency',
            'prices.customerGroup',
        ])
        ->findOrFail($variant->getKey());

    $highestPrice = $variant->prices->sortByDesc(fn ($price) => $price->price->value)->first();

    $customerGroup = CustomerGroup::factory()
        ->create();

    $highestPrice->update([
        'customer_group_id' => $customerGroup->getKey(),
    ]);

    $highestBasePrice = $variant->prices
        ->filter(fn ($price) => $price->getKey() !== $highestPrice->getKey())
        ->sortByDesc(fn ($price) => $price->price->value)
        ->first();

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/highest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($highestBasePrice)
        ->assertDoesntHaveIncluded();

    App::make(StorefrontSessionInterface::class)
        ->setCustomerGroups(Collection::make([$customerGroup]));

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/highest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($highestPrice)
        ->assertDoesntHaveIncluded();
});

afterEach(function () {
    App::make(StorefrontSessionInterface::class)->forget();
});
