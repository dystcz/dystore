<?php

namespace Dystore\Api\Domain\JsonApi\Eloquent\Fields;

use Closure;
use Dystore\Api\Domain\Discounts\Data\DiscountBreakdown;
use LaravelJsonApi\Contracts\Resources\Serializer\Attribute as SerializableContract;
use LaravelJsonApi\Eloquent\Fields\ArrayHash;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Map;
use Lunar\Base\ValueObjects\Cart\DiscountBreakdown as LunarDiscountBreakdown;

class CartPricing extends Map implements SerializableContract
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
            Price::make(fieldName: 'shipping_sub_total', price: $owner->shippingSubTotal),
            Price::make(fieldName: 'shipping_tax_total', price: $owner->shippingTaxTotal),
            Price::make(fieldName: 'shipping_total', price: $owner->shippingTotal),
            Price::make(fieldName: 'payment_sub_total', price: $owner->paymentSubTotal),
            Price::make(fieldName: 'payment_tax_total', price: $owner->paymentTaxTotal),
            Price::make(fieldName: 'payment_total', price: $owner->paymentTotal),
            Price::make(fieldName: 'sub_total', price: $owner->subTotal),
            Price::make(fieldName: 'sub_total_discounted', price: $owner->subTotalDiscounted),
            Price::make(fieldName: 'total', price: $owner->total),
            Price::make(fieldName: 'tax_total', price: $owner->taxTotal),
            Price::make(fieldName: 'discount_total', price: $owner->discountTotal),

            ArrayHash::make('tax_breakdown', 'taxBreakdown')
                ->serializeUsing(
                    static fn ($value) => $value?->amounts
                ),

            ArrayHash::make('discount_breakdown', 'discountBreakdown')
                ->serializeUsing(
                    static fn ($value) => $value?->map(
                        fn (LunarDiscountBreakdown $discountBreakdown) => (new DiscountBreakdown($discountBreakdown))->toArray(),
                    )
                ),
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
