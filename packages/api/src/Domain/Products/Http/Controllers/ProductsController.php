<?php

namespace Dystore\Api\Domain\Products\Http\Controllers;

use Dystore\Api\Base\Controller;
use Dystore\Api\Domain\Products\Contracts\ProductsController as ProductsControllerContract;
use Dystore\Api\Domain\Products\JsonApi\V1\ProductQuery;
use Dystore\Api\Domain\Products\Models\Product;
use Illuminate\Support\Facades\App;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchMany;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchRelated;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchRelationship;
use Lunar\Models\Contracts\Product as ProductContract;

class ProductsController extends Controller implements ProductsControllerContract
{
    use FetchMany;
    use FetchOne;
    use FetchRelated;
    use FetchRelationship;

    public function read(?ProductContract $product, ProductQuery $query): void
    {
        /** @var Product $product */
        $productId = $product?->getKey();

        if ($productId && App::has('lunar-api-product-views')) {
            dispatch(fn () => App::get('lunar-api-product-views')->record($productId));
        }
    }
}
