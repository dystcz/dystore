<?php

namespace Dystore\Api\Domain\JsonApi\Eloquent\Fields;

use Closure;
use LaravelJsonApi\Contracts\Resources\Serializer\Attribute as SerializableContract;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Map;
use LaravelJsonApi\Eloquent\Fields\Number;

class OrderPricing extends Map implements SerializableContract
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
        /** @var \Lunar\Models\Contracts\Cart $owner */
        $owner = $this->related ? $model->{$this->related} : $model;
        $values = [];

        $fields = [
            Boolean::make('incl_tax')->extractUsing(fn () => prices_inc_tax()),
            Price::make(fieldName: 'shipping_total', price: $owner->shipping_total),
            Price::make(fieldName: 'payment_total', price: $owner->payment_total),
            Price::make(fieldName: 'tax_total', price: $owner->tax_total),
            Price::make(fieldName: 'sub_total', price: $owner->sub_total),
            Price::make(fieldName: 'discount_total', price: $owner->discount_total),
            Price::make(fieldName: 'total', price: $owner->total),

            ArrayHash::make('tax_breakdown', 'taxBreakdown')->serializeUsing(static fn ($value) => $value?->amounts),
            ArrayHash::make('shipping_breakdown')->serializeUsing(static fn ($value) => $value?->items),
            ArrayHash::make('payment_breakdown')->serializeUsing(static fn ($value) => $value?->items),
            ArrayHash::make('discount_breakdown', 'discountBreakdown')->serializeUsing(static fn ($value) => $value),
            Number::make('exchange_rate'),
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
