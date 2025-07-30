<?php

namespace Dystore\Api\Domain\JsonApi\Eloquent\Fields;

use Closure;
use LaravelJsonApi\Contracts\Resources\Serializer\Attribute as SerializableContract;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Map;
use LaravelJsonApi\Eloquent\Fields\Number;
use Lunar\DataTypes\Price as PriceData;

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

        $subTotalDiscounted = new PriceData(
            value: $owner->sub_total->value - $owner->discount_total->value,
            currency: $owner->currency
        );

        $fields = [
            Boolean::make('incl_tax')->extractUsing(fn () => prices_inc_tax()),
            Price::make(fieldName: 'unit_price', price: $owner->unit_price),
            Price::make(fieldName: 'sub_total', price: $owner->sub_total),
            Price::make(fieldName: 'sub_total_discounted', price: $subTotalDiscounted),
            Price::make(fieldName: 'total', price: $owner->total),
            Price::make(fieldName: 'tax_total', price: $owner->tax_total),
            Price::make(fieldName: 'discount_total', price: $owner->discount_total),
            Number::make('quantity'),
        ];

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
