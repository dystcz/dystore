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

it('can read lowest price through relationship', function () {
    /** @var TestCase $this */
    /** @var \Dystore\Api\Domain\ProductVariants\Models\ProductVariant $variant */
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
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/lowest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($variant->prices->sortBy('price')->first())
        ->assertDoesntHaveIncluded();
});

it('can read correct lowest price when customer group is set', function () {
    /** @var TestCase $this */
    /** @var \Dystore\Api\Domain\ProductVariants\Models\ProductVariant $variant */
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

    $lowestPrice = $variant->prices->sortBy(fn ($price) => $price->price->value)->first();

    $customerGroup = CustomerGroup::factory()
        ->create();

    $lowestPrice->update([
        'customer_group_id' => $customerGroup->getKey(),
    ]);

    $lowestBasePrice = $variant->prices
        ->filter(fn ($price) => $price->getKey() !== $lowestPrice->getKey())
        ->sortBy(fn ($price) => $price->price->value)
        ->first();

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/lowest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($lowestBasePrice)
        ->assertDoesntHaveIncluded();

    App::make(StorefrontSessionInterface::class)
        ->setCustomerGroups(Collection::make([$customerGroup]));

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/product_variants/{$variant->getRouteKey()}/lowest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($lowestPrice)
        ->assertDoesntHaveIncluded();
});
