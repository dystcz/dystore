<?php

namespace Dystcz\LunarApi\Domain\Carts\Data;

use Dystcz\LunarApi\Domain\Carts\JsonApi\V1\CartLineRequest;
use Illuminate\Support\Arr;

class CartLineData
{
    public function __construct(
        public string $purchasable_type,
        public int $purchasable_id,
        public int $quantity = 1,
        public ?array $meta = null,
    ) {
    }

    public static function fromRequest(CartLineRequest $request): CartLineData
    {
        return new self(
            ...Arr::only($request->validationData(), ['purchasable_type', 'purchasable_id', 'quantity', 'meta'])
        );
    }

    public function toArray(): array
    {
        return [
            'purchasable_type' => $this->purchasable_type,
            'purchasable_id' => $this->purchasable_id,
            'quantity' => $this->quantity,
            'meta' => $this->meta,
        ];
    }
}
