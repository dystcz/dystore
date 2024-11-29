<?php

namespace Dystore\Api\Domain\Addresses\Http\Controllers;

use Dystore\Api\Base\Controller;
use Dystore\Api\Domain\Addresses\Contracts\AddressesController as AddressesControllerContract;
use Dystore\Api\Domain\Addresses\JsonApi\V1\AddressQuery;
use Dystore\Api\Domain\Addresses\JsonApi\V1\AddressRequest;
use Dystore\Api\Domain\Addresses\JsonApi\V1\AddressSchema;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\Destroy;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchMany;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchRelated;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchRelationship;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\Store;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\Update;
use Lunar\Models\Address;
use Lunar\Models\Contracts\Address as AddressContract;

class AddressesController extends Controller implements AddressesControllerContract
{
    use Destroy;
    use FetchMany;
    use FetchOne;
    use FetchRelated;
    use FetchRelationship;
    use Store;
    use Update;

    /**
     * Create a new resource.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\Response
     */
    public function store(
        AddressSchema $schema,
        AddressRequest $request,
        AddressQuery $query,
    ): DataResponse {
        $this->authorize('create', Address::modelClass());

        $meta = [
            'company_in' => $request->validated('company_in', null),
            'company_tin' => $request->validated('company_tin', null),
        ];

        $model = $schema
            ->repository()
            ->create()
            ->withRequest($query)
            ->store(
                array_merge(
                    $request->validated(),
                    ['meta' => $meta],
                ),
            );

        return DataResponse::make($model)
            ->didCreate();
    }

    /**
     * Update an existing resource.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\Response
     */
    public function update(
        AddressSchema $schema,
        AddressRequest $request,
        AddressQuery $query,
        AddressContract $address,
    ): DataResponse {
        $this->authorize('update', $address);

        $meta = array_merge($address->meta?->toArray() ?? [], [
            'company_in' => $request->validated('company_in', null),
            'company_tin' => $request->validated('company_tin', null),
        ]);

        $model = $schema
            ->repository()
            ->update($address)
            ->withRequest($query)
            ->store(
                array_merge(
                    $request->validated(),
                    ['meta' => $meta],
                ),
            );

        return DataResponse::make($model)
            ->didntCreate();
    }
}
