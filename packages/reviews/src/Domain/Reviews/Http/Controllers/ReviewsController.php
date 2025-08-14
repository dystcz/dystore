<?php

namespace Dystore\Reviews\Domain\Reviews\Http\Controllers;

use Dystore\Api\Base\Controller;
use Dystore\Reviews\Domain\Reviews\Contacts\ReviewsController as ReviewsControllerContract;
use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Core\Responses\ErrorResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\Destroy;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchMany;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\Store;

class ReviewsController extends Controller implements ReviewsControllerContract
{
    use Destroy;
    use FetchMany;
    use FetchOne;
    use Store;

    /**
     * Handle authentication before creating a review.
     *
     * @param  \LaravelJsonApi\Laravel\Http\Requests\ResourceRequest  $request
     * @param  \LaravelJsonApi\Laravel\Http\Requests\ResourceQuery  $query
     */
    protected function creating($request, $query): ?ErrorResponse
    {
        if (Config::get('dystore.reviews.domains.reviews.settings.auth_required', true)) {
            if (! $request->user()) {
                return ErrorResponse::make([[
                    'detail' => 'Unauthenticated.',
                    'status' => '401',
                    'title' => 'Unauthorized',
                ]])->withStatus(401);
            }
        }

        return null;
    }
}
