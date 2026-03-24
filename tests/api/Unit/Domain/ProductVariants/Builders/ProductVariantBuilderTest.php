<?php

use Dystore\Api\Base\Enums\PurchasableStatus;
use Dystore\Api\Domain\Prices\Factories\PriceFactory;
use Dystore\Api\Domain\Products\Factories\ProductFactory;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('product_variants', 'availability');

it('can scope always purchasable variants', function () {
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

    expect(ProductVariant::query()->alwaysPurchasable()->count())->toBe(1);
});

it('can scope in stock variants', function () {
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

    expect(ProductVariant::query()->inStock()->count())->toBe(1);
});

it('can scope backorderable variants', function () {
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

    expect(ProductVariant::query()->backorderable()->count())->toBe(1);
});

it('can scope available variants', function () {
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
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 5,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants',
        )
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'backorder' => 3,
                    'purchasable' => PurchasableStatus::BACKORDER->value,
                ]),
            'variants',
        )
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

    expect(ProductVariant::query()->available()->count())->toBe(3);
});
