<?php

namespace Dystore\Api\Domain\JsonApi\Eloquent\Fields;

use Closure;
use LaravelJsonApi\Contracts\Resources\Serializer\Attribute as SerializableContract;
use LaravelJsonApi\Eloquent\Fields\Map;
use LaravelJsonApi\Eloquent\Fields\Number;
use Lunar\DataTypes\Price as PriceData;

// TODO: Finish
class OrderLinePricing extends Map implements SerializableContract
{
    private ?Closure $extractor = null;

    private ?string $related = null;

    public function __construct(private string $fieldName, private array $map = [])
    {
        parent::__construct($fieldName, $map);
    }

    public static function make(string $fieldName, array $map = []): self
    {
        return new self($fieldName, $map);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(object $model): ?array
    {
        /** @var \Lunar\Models\Contracts\OrderLine $owner */
        $owner = $this->related ? $model->{$this->related} : $model;
        $values = [];

        $inclTax = prices_inc_tax();

        $curency = $owner->currency;
        $quantity = $owner->quantity;

        $unitPrice = $owner->unit_price;
        $subTotal = $owner->sub_total;
        $total = $owner->total;

        $discountTotal = $owner->discount_total;
        $taxTotal = $owner->tax_total;

        // $subTotalDiscounted = new PriceData(
        //     value: $subTotal->value - $discountTotal->value,
        //     currency: $curency
        // );

        $totalNoTax = new PriceData(
            value: $total->value - $taxTotal->value,
            currency: $curency,
            unitQty: 1
        );

        $unitPriceNoTax = new PriceData(
            value: $totalNoTax->value,
            currency: $curency,
            unitQty: $quantity
        );

        ray($unitPriceNoTax);
        ray($unitPriceNoTax->unitDecimal());

        $fields = [
            Price::make(fieldName: 'unit_price', price: $unitPrice),
            Price::make(fieldName: 'unit_price_no_tax', price: $unitPriceNoTax),
            Price::make(fieldName: 'sub_total', price: $subTotal),
            // Price::make(fieldName: 'sub_total_discounted', price: $subTotalDiscounted),
            Price::make(fieldName: 'total', price: $total),
            Price::make(fieldName: 'total_no_tax', price: $totalNoTax),
            Price::make(fieldName: 'tax_total', price: $taxTotal),
            Price::make(fieldName: 'discount_total', price: $discountTotal),

            Number::make('quantity'),
        ];

        /** We intentionally use a single loop for serialization efficiency. */
        if ($owner) {
            foreach ($fields as $attr) {
                if ($attr instanceof SerializableContract) {
                    $values[$attr->serializedFieldName()] = $attr->serialize($owner);
                }
            }
        }

        ksort($values);

        return $values ?: null;
    }
}
