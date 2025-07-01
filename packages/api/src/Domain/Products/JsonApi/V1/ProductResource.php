<?php

namespace Dystore\Api\Domain\Products\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Resources\JsonApiResource;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Illuminate\Http\Request;
use Lunar\Facades\CartSession;
use Lunar\Facades\Pricing;
use Lunar\Facades\StorefrontSession;
use Lunar\Models\Contracts\ProductVariant as ProductVariantContract;

/**
 * @param  \Dystore\Api\Domain\Products\Models\Product  $resource
 */
class ProductResource extends JsonApiResource
{
    /**
     * Get the resource's attributes.
     *
     * @param  Request|null  $request
     */
    public function attributes($request): iterable
    {
        $model = $this->resource;

        // $variant = $this->variants->firstWhere('id', 2099);

        // ray(CartSession::current()->lines->first()->total);

        // $pricing = Pricing::for($variant)
        //     ->currency(StorefrontSession::getCurrency())
        //     ->customerGroups(StorefrontSession::getCustomerGroups())
        //     ->get();
        //
        // ray($pricing);

        if ($model->relationLoaded('variants')) {
            /** @var ProductVariant $variant */
            $model->variants->each(
                fn (ProductVariantContract $variant) => $variant->setRelation('product', $model),
            );
        }

        if ($model->relationLoaded('cheapestVariant')) {
            $model->cheapestVariant->setRelation('product', $model);
        }

        if ($model->relationLoaded('mostExpensiveVariant')) {
            $model->mostExpensiveVariant->setRelation('product', $model);
        }

        return parent::attributes($request);
    }
}
