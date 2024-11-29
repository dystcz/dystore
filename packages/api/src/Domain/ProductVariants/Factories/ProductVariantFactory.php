<?php

namespace Dystore\Api\Domain\ProductVariants\Factories;

use Dystore\Api\Domain\Media\Factories\MediaFactory;
use Dystore\Api\Domain\Prices\Models\Price;
use Dystore\Api\Domain\Products\Models\Product;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunar\FieldTypes\Text;
use Lunar\Models\Currency;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRateAmount;

class ProductVariantFactory extends \Lunar\Database\Factories\ProductVariantFactory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::modelClass()::factory(),
            'tax_class_id' => TaxClass::modelClass()::factory()
                ->hasTaxRateAmounts(TaxRateAmount::modelClass()::factory()),
            'sku' => Str::random(12),
            'unit_quantity' => 1,
            'gtin' => $this->faker->unique()->isbn13,
            'mpn' => $this->faker->unique()->isbn13,
            'ean' => $this->faker->unique()->ean13,
            'shippable' => true,
            'attribute_data' => collect([
                'name' => new Text($this->faker->name),
                'description' => new Text($this->faker->sentence),
            ]),
        ];
    }

    /**
     * Create a model with a price.
     */
    public function withPrice(?int $price = null, ?int $comparePrice = null): static
    {
        return $this->has(
            Price::modelClass()::factory()->state([
                'price' => $price ?? $this->faker->numberBetween(100, 1000),
                'compare_price' => $comparePrice ?? $this->faker->numberBetween(100),
                'currency_id' => Currency::modelClass()::getDefault(),
            ]),
        );
    }

    /**
     * Create a model with thumbnail.
     *
     * @param  array<string,mixed>  $state
     */
    public function withThumbnail(array $state = []): ProductVariantFactory
    {
        $prefix = Config::get('lunar.database.table_prefix');

        return $this->hasAttached(
            MediaFactory::new()->state(fn ($data, $model) => array_merge([
                'model_id' => $model->id,
                'model_type' => (new (ProductVariant::modelClass()))->getMorphClass(),
                'collection_name' => 'images',
            ]), $state),
            ['primary' => true],
            'images',
        );
    }

    /**
     * Create a model with images.
     *
     * @param  array<string,mixed>  $state
     */
    public function withImages(int $count = 1, array $state = []): ProductVariantFactory
    {
        $prefix = Config::get('lunar.database.table_prefix');

        return $this
            ->has(
                MediaFactory::new()
                    ->state(fn ($data, $model) => array_merge([
                        'model_id' => $model->id,
                        'model_type' => (new (ProductVariant::modelClass()))->getMorphClass(),
                        'collection_name' => 'images',
                        'custom_properties' => ['test' => true],
                    ]), $state)
                    ->count($count),
                'images',
            );
    }
}
