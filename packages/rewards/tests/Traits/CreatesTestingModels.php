<?php

namespace Dystcz\LunarRewards\Tests\Traits;

use Dystcz\LunarApi\Domain\Carts\Models\Cart;
use Illuminate\Database\Eloquent\Model;
use Lunar\DataTypes\Price as PriceDataType;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Order;
use Lunar\Models\Price;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxRateAmount;

trait CreatesTestingModels
{
    /**
     * @param  array<string,Model>  $models
     */
    public function createCart(...$models): Cart
    {
        $customerGroup = CustomerGroup::factory()->create([
            'default' => true,
        ]);

        $currency = $this->modelInParams('currency', $models)
            ?? Currency::factory()->create([
                'decimal_places' => 2,
            ]);

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $taxClass = TaxClass::factory()->create([
            'name' => 'Foobar',
        ]);

        $taxClass->taxRateAmounts()->create(
            TaxRateAmount::factory()->make([
                'percentage' => 20,
                'tax_class_id' => $taxClass->id,
            ])->toArray()
        );

        $purchasable = ProductVariant::factory()->create([
            'tax_class_id' => $taxClass->id,
            'unit_quantity' => 1,
        ]);

        $price = Price::factory()->create([
            'price' => 2000,
            'tier' => 1,
            'currency_id' => $currency->id,
            'priceable_type' => get_class($purchasable),
            'priceable_id' => $purchasable->id,
        ]);

        $cart->lines()->create([
            'purchasable_type' => get_class($purchasable),
            'purchasable_id' => $purchasable->id,
            'quantity' => 1,
        ]);

        return $cart->calculate();
    }

    /**
     * @param  array<string,Model>  $models
     */
    public function createOrder(...$models): Order
    {
        $customerGroup = CustomerGroup::factory()->create([
            'default' => true,
        ]);

        $billing = CartAddress::factory()->make([
            'type' => 'billing',
            'country_id' => Country::factory(),
            'first_name' => 'Santa',
            'line_one' => '123 Elf Road',
            'city' => 'Lapland',
            'postcode' => 'BILL',
        ]);

        $shipping = CartAddress::factory()->make([
            'type' => 'shipping',
            'country_id' => Country::factory(),
            'first_name' => 'Santa',
            'line_one' => '123 Elf Road',
            'city' => 'Lapland',
            'postcode' => 'SHIPP',
        ]);

        $currency = $this->modelInParams('currency', $models)
            ?? Currency::factory()->create([
                'decimal_places' => 2,
            ]);

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $taxClass = TaxClass::factory()->create([
            'name' => 'Foobar',
        ]);

        $taxClass->taxRateAmounts()->create(
            TaxRateAmount::factory()->make([
                'percentage' => 20,
                'tax_class_id' => $taxClass->id,
            ])->toArray()
        );

        $purchasable = ProductVariant::factory()->create([
            'tax_class_id' => $taxClass->id,
            'unit_quantity' => 1,
        ]);

        $price = Price::factory()->create([
            'price' => 1000,
            'tier' => 1,
            'currency_id' => $currency->id,
            'priceable_type' => get_class($purchasable),
            'priceable_id' => $purchasable->id,
        ]);

        $cart->lines()->create([
            'purchasable_type' => get_class($purchasable),
            'purchasable_id' => $purchasable->id,
            'quantity' => 1,
        ]);

        $cart->addresses()->createMany([
            $billing->toArray(),
            $shipping->toArray(),
        ]);

        $shippingOption = new ShippingOption(
            name: 'Basic Delivery',
            description: 'Basic Delivery',
            identifier: 'BASDEL',
            price: new PriceDataType(500, $cart->currency, 1),
            taxClass: $taxClass
        );

        ShippingManifest::addOption($shippingOption);

        $cart->shippingAddress->update([
            'shipping_option' => $shippingOption->getIdentifier(),
        ]);

        $cart->shippingAddress->shippingOption = $shippingOption;

        $order = $cart->createOrder();

        return $order;
    }

    /**
     * Get model from function parameters.
     *
     * @param  array<string,Model>  $params
     */
    private function modelInParams(string $model, array $params): ?Model
    {
        if (empty($params)) {
            return null;
        }

        if (array_key_exists($model, $params)) {
            return $params[$model];
        }

        return null;
    }
}
