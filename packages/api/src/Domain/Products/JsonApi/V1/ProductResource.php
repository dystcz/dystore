<?php

namespace Dystore\Api\Domain\Products\JsonApi\V1;

use Dystore\Api\Domain\JsonApi\Resources\JsonApiResource;
use Dystore\Api\Domain\Products\Models\Product;
use Dystore\Api\Domain\ProductVariants\Models\ProductVariant;
use Illuminate\Http\Request;
use Lunar\Models\Contracts\ProductVariant as ProductVariantContract;

class ProductResource extends JsonApiResource
{
    /**
     * Get the resource's attributes.
     *
     * @param  Request|null  $request
     */
    public function attributes($request): iterable
    {
        /** @var Product $model */
        $model = $this->resource;

        if ($model->relationLoaded('variants')) {
            /** @var ProductVariant $variant */
            $model->variants->each(
                fn (ProductVariantContract $variant) => $variant->setRelation('product', $model),
            );
        }

        if ($model->relationLoaded('cheapestVariant')) {
            $model->cheapestVariant->setRelation('product', $model);
        }

        return parent::attributes($request);
    }
}
