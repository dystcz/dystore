<?php

namespace Dystore\Reviews\Domain\Reviews\Http\Controllers;

use Dystore\Api\Base\Controller;
use Illuminate\Support\Facades\Config;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\Destroy;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchMany;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\Store;

class ReviewsController extends Controller
{
    use Destroy;
    use FetchMany;
    use FetchOne;
    use Store;

    public function __construct()
    {
        if (Config::get('dystore.reviews.domains.reviews.settings.auth_required', true)) {
            $this
                ->middleware(Config::get(
                    'dystore.reviews.domains.reviews.settings.auth_middleware',
                    ['auth'],
                ))
                ->only('store');
        }
    }
}
