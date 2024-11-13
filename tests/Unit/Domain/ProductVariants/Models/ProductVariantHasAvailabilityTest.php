<?php

use Dystore\Api\Base\Enums\PurchasableStatus;
use Dystore\Api\Domain\Prices\Factories\PriceFactory;
use Dystore\Api\Domain\Products\Enums\Availability;
use Dystore\Api\Domain\Products\Factories\ProductFactory;
use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Factories\ProductVariantFactory;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Lunar\FieldTypes\Text;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

uses(TestCase::class, RefreshDatabase::class, WithFaker::class);

it('can determine variant is always purchasable', function () {
    /** @var TestCase $this */
    /** @var Product $product */
    $product = ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::ALWAYS->value,
                ]),
            'variants'
        )
        ->create();

    /** @var ProductVariant $variant */
    $variant = $product->variants->first();

    assertTrue($variant->availability === Availability::ALWAYS);
})->group('product_variants', 'availability');

it('can determine variant is in stock', function () {
    /** @var TestCase $this */
    /** @var Product $product */
    $product = ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants'
        )
        ->create();

    /** @var ProductVariant $variant */
    $variant = $product->variants->first();

    assertFalse($variant->availability === Availability::IN_STOCK);

    $variant->update([
        'stock' => 1,
    ]);

    $variant->refresh();

    assertTrue($variant->availability === Availability::IN_STOCK);

})->group('product_variants', 'availability');

it('can determine variant is backorder', function () {
    /** @var TestCase $this */
    /** @var Product $product */
    $product = ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'backorder' => 0,
                    'purchasable' => PurchasableStatus::BACKORDER->value,
                ]),
            'variants'
        )
        ->create();

    /** @var ProductVariant $variant */
    $variant = $product->variants->first();

    assertFalse($variant->availability === Availability::BACKORDER);

    $variant->update([
        'backorder' => 1,
    ]);

    $variant->refresh();

    assertTrue($variant->availability === Availability::BACKORDER);

})->group('product_variants', 'availability');

it('can determine variant is preorder', function () {
    /** @var TestCase $this */
    /** @var Product $product */
    $product = ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants'
        )
        ->create([
            'attribute_data' => collect([
                'name' => new Text($this->faker->name),
                'description' => new Text($this->faker->sentence),
                'eta' => new Text($this->faker->date),
            ]),
        ]);

    /** @var ProductVariant $variant */
    $variant = $product->variants->first();

    assertFalse($variant->availability === Availability::PREORDER);

    $variant->update([
        'stock' => 1,
    ]);

    $variant->refresh();

    assertTrue($variant->availability === Availability::PREORDER);

    $variant->update([
        'stock' => 0,
        'purchasable' => PurchasableStatus::BACKORDER->value,
        'backorder' => 1,
    ]);

    $variant->refresh();

    assertTrue($variant->availability === Availability::PREORDER);

})->group('product_variants', 'availability');

it('can determine variant is out of stock', function () {
    /** @var TestCase $this */
    /** @var Product $product */
    $product = ProductFactory::new()
        ->has(
            ProductVariantFactory::new()
                ->has(PriceFactory::new())
                ->state([
                    'stock' => 0,
                    'purchasable' => PurchasableStatus::IN_STOCK->value,
                ]),
            'variants'
        )
        ->create();

    /** @var ProductVariant $variant */
    $variant = $product->variants->first();

    assertTrue($variant->availability === Availability::OUT_OF_STOCK);

    $variant->update([
        'purchasable' => 'random',
    ]);

    $variant->refresh();

    assertTrue($variant->availability === Availability::OUT_OF_STOCK);

    $variant->update([
        'purchasable' => PurchasableStatus::BACKORDER->value,
        'backorder' => 0,
    ]);

    $variant->refresh();

    assertTrue($variant->availability === Availability::OUT_OF_STOCK);

})->group('product_variants', 'availability');
