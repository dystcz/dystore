<?php

use Dystore\Api\Base\Enums\PurchasableStatus;
use Dystore\Api\Domain\Prices\Factories\PriceFactory;
use Dystore\Api\Domain\Products\Factories\ProductFactory;
use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('products', 'availability');

it('can scope always purchasable products', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::ALWAYS->value,
                ]),
            'variants',
        )
        ->create();

    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 5,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->alwaysPurchasable()->count())->toBe(1);
});

it('can scope in stock products', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 5,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->create();

    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::ALWAYS->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->inStock()->count())->toBe(1);
});

it('excludes out of stock products from in stock scope', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->inStock()->count())->toBe(0);
});

it('can scope backorderable products', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'backorder' => 5,
                    'purchasable' => PurchasableStatus::BACKORDER->value,
                ]),
            'variants',
        )
        ->create();

    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 5,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->backorderable()->count())->toBe(1);
});

it('excludes products with zero backorder from backorderable scope', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'backorder' => 0,
                    'purchasable' => PurchasableStatus::BACKORDER->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->backorderable()->count())->toBe(0);
});

it('can scope available products with always purchasable variant', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::ALWAYS->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->available()->count())->toBe(1);
});

it('can scope available products with in stock variant', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 5,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->available()->count())->toBe(1);
});

it('can scope available products with backorderable variant', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'backorder' => 5,
                    'purchasable' => PurchasableStatus::BACKORDER->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->available()->count())->toBe(1);
});

it('excludes out of stock products from available scope', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->available()->count())->toBe(0);
});

it('excludes products with zero backorder from available scope', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'backorder' => 0,
                    'purchasable' => PurchasableStatus::BACKORDER->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->available()->count())->toBe(0);
});

it('includes product when at least one variant is available', function () {
    /** @var TestCase $this */
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ])
                ->count(3),
            'variants',
        )
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 1,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->available()->count())->toBe(1);
});

it('filters multiple products correctly with available scope', function () {
    /** @var TestCase $this */

    // Available: always purchasable.
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::ALWAYS->value,
                ]),
            'variants',
        )
        ->create();

    // Available: in stock.
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 10,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->create();

    // Not available: out of stock.
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->create();

    // Available: backorderable.
    ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'backorder' => 3,
                    'purchasable' => PurchasableStatus::BACKORDER->value,
                ]),
            'variants',
        )
        ->create();

    expect(Product::query()->available()->count())->toBe(3);
});
