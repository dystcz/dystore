<?php

namespace Dystore\Api\Domain\JsonApi\Eloquent\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonApi\Core\Json\Hash;
use LaravelJsonApi\Eloquent\Fields\Attribute;
use Lunar\DataTypes\Price as PriceData;
use UnexpectedValueException;

class Price extends Attribute
{
    private ?Closure $deserializer = null;

    private ?Closure $serializer = null;

    private ?Closure $hydrator = null;

    private ?Closure $extractor = null;

    private bool $force = false;

    private ?string $related = null;

    public function __construct(
        private string $fieldName,
        private ?string $column = null,
        private ?PriceData $price = null,
    ) {
        parent::__construct($fieldName, $column);

        $this->extractor = static function (Model $model, string $column, ?PriceData $price): array {
            return [
                'value' => $price->value,
                'decimal' => $price->unitDecimal(),
                'formatted' => $price->unitFormatted(),
            ];
        };
    }

    public static function make(
        string $fieldName,
        ?string $column = null,
        ?PriceData $price = null
    ): static {
        return new static($fieldName, $column, $price);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize(object $model): array
    {
        $column = $this->column();
        $owner = $this->related ? $model->{$this->related} : $model;
        $value = $this->price ?? $owner?->{$column} ?? null;

        if ($this->serializer) {
            $value = ($this->serializer)($value);
        }

        if ($this->extractor) {
            return ($this->extractor)($model, $column, $value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    protected function deserialize($value): ?array
    {
        $value = parent::deserialize($value);

        if (is_null($value)) {
            return null;
        }

        if ($value && $this->keys) {
            $value = ($this->keys)($value);
        }

        if (is_null($value)) {
            return null;
        }

        return Hash::cast($value)->all();
    }

    /**
     * {@inheritDoc}
     */
    protected function assertValue($value): void
    {
        if (! $value instanceof PriceData) {
            throw new UnexpectedValueException(sprintf(
                'Expecting the value of attribute %s to be of Lunar\DataTypes\Price type.',
                $this->name()
            ));
        }
    }
}
