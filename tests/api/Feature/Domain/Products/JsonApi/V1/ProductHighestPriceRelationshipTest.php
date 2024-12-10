<?php

use Dystore\Api\Domain\Products\Factories\ProductFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
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
