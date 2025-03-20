<?php

use Dystore\Api\Domain\Products\Factories\ProductFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Models\Contracts\Price as PriceContract;
use Lunar\Models\CustomerGroup;

uses(TestCase::class, RefreshDatabase::class)
    ->group('products', 'prices');

it('can read lowest price through relationship', function () {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/lowest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product->prices->sortBy('price')->first())
        ->assertDoesntHaveIncluded();
});

it('can read correct lowest price when customer group is set', function () {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $lowestPrice = $product->prices->sortBy(fn ($price) => $price->price->value)->first();

    $customerGroup = CustomerGroup::factory()
        ->create();

    $lowestPrice->update([
        'customer_group_id' => $customerGroup->getKey(),
    ]);

    $lowestBasePrice = $product->prices
        ->filter(fn ($price) => $price->getKey() !== $lowestPrice->getKey())
        ->sortBy(fn ($price) => $price->price->value)
        ->first();

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/lowest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($lowestBasePrice)
        ->assertDoesntHaveIncluded();

    App::make(StorefrontSessionInterface::class)
        ->setCustomerGroups(Collection::make([$customerGroup]));

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/lowest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($lowestPrice)
        ->assertDoesntHaveIncluded();
});

it('can read lowest price through relationship with includes', function (string $includePath, string $type, callable $getModel) {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $customerGroup = CustomerGroup::factory()->create();

    App::make(StorefrontSessionInterface::class)->setCustomerGroups(Collection::make([$customerGroup]));

    $price = $product->prices->sortBy('price')->first();

    $price->update([
        'customer_group_id' => $customerGroup->getKey(),
    ]);

    $response = $this
        ->jsonApi()
        ->includePaths($includePath)
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/lowest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($price)
        ->assertIncluded([
            ['type' => $type, 'id' => $getModel($price)->getRouteKey()],
        ]);
})->with([
    'currency' => ['currency', 'currencies', fn (PriceContract $price) => $price->currency],
    'customer_group' => ['customer_group', 'customer_groups', fn (PriceContract $price) => $price->customerGroup],
]);
