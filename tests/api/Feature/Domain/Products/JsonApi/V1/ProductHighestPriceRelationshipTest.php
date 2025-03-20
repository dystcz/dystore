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

it('can read highest price through relationship', function () {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/highest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($product->prices->sortByDesc('price')->first())
        ->assertDoesntHaveIncluded();
});

it('can read correct highest price when customer group is set', function () {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices(3)
        ->create();

    $highestPrice = $product->prices->sortByDesc(fn ($price) => $price->price->value)->first();

    $customerGroup = CustomerGroup::factory()
        ->create();

    $highestPrice->update([
        'customer_group_id' => $customerGroup->getKey(),
    ]);

    $highestBasePrice = $product->prices
        ->filter(fn ($price) => $price->getKey() !== $highestPrice->getKey())
        ->sortByDesc(fn ($price) => $price->price->value)
        ->first();

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/highest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($highestBasePrice)
        ->assertDoesntHaveIncluded();

    App::make(StorefrontSessionInterface::class)
        ->setCustomerGroups(Collection::make([$customerGroup]));

    $response = $this
        ->jsonApi()
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/highest_price"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($highestPrice)
        ->assertDoesntHaveIncluded();
});

it('can read highest price through relationship with includes', function (string $includePath, string $type, callable $getModel) {
    /** @var TestCase $this */
    $product = ProductFactory::new()
        ->withPrices()
        ->create();

    $customerGroup = CustomerGroup::factory()->create();

    App::make(StorefrontSessionInterface::class)->setCustomerGroups(Collection::make([$customerGroup]));

    $price = $product->prices->sortByDesc('price')->first();

    $price->update([
        'customer_group_id' => $customerGroup->getKey(),
    ]);

    $response = $this
        ->jsonApi()
        ->includePaths($includePath)
        ->expects('prices')
        ->get(serverUrl("/products/{$product->getRouteKey()}/highest_price"));

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

afterEach(function () {
    App::make(StorefrontSessionInterface::class)->forget();
});
