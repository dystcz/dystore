<?php

namespace Modules\DystoreAres\Domain\Ares\Http\Controllers;

use Dystcz\LunarApi\Base\Controller;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchMany;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use Modules\DystoreAres\Domain\Ares\Actions\GetCompanyInfoFromAres;
use Modules\DystoreAres\Domain\Ares\Contracts\AresController as AresControllerContract;
use Modules\DystoreAres\Domain\Ares\JsonApi\V1\AresRequest;

class AresController extends Controller implements AresControllerContract
{
    use FetchMany;
    use FetchOne;

    /**
     * Get company info from Ares.
     */
    public function getCompanyInfo(AresRequest $request, GetCompanyInfoFromAres $action): DataResponse
    {
        $record = $action->handle($request->validated('company_in'));

        return DataResponse::make($record)
            ->didntCreate();
    }
}
