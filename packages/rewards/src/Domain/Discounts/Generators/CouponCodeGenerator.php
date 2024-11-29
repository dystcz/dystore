<?php

namespace Dystcz\LunarRewards\Domain\Discounts\Generators;

use Dystcz\LunarRewards\Domain\Discounts\Contracts\CouponCodeGenerator as CouponCodeGeneratorContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Lunar\Models\Discount;

class CouponCodeGenerator implements CouponCodeGeneratorContract
{
    public const LENGTH = 16;

    public function __construct(
        protected ?Model $model = null,
    ) {
    }

    /**
     * Generate discount code.
     */
    public function generate(): string
    {
        $code = null;
        $exists = true;

        while (true) {
            if (! is_null($code) && ! $exists) {
                break;
            }

            $code = $this->generateCode();

            $exists = Discount::query()
                ->where('coupon', $code)
                ->exists();
        }

        return $code;
    }

    /**
     * Generate code.
     */
    protected function generateCode(): string
    {
        return Str::upper(Str::random(static::LENGTH));
    }

    /**
     * Static constructor.
     */
    public static function of(Model $model): self
    {
        return new static($model);
    }

    /**
     * Get model.
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }
}
