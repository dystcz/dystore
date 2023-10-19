<?php

namespace Dystcz\LunarApi\Domain\Prices\Factories;

use Dystcz\LunarApi\Domain\Prices\Models\Price;
use Lunar\Models\Currency;

class PriceFactory extends \Lunar\Database\Factories\PriceFactory
{
    protected $model = Price::class;

    public function definition(): array
    {
        return [
            'price' => $price ?? $this->faker->numberBetween(1, 2500),
            'compare_price' => $comparePrice ?? $this->faker->numberBetween(1, 2500),
            'currency_id' => Currency::getDefault() ?? Currency::factory(),
        ];
    }
}