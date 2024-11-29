<?php

namespace Dystcz\LunarApiReviews\Domain\ProductVariants\Http\Controllers;

use Dystcz\LunarApi\Controller;
use LaravelJsonApi\Laravel\Http\Controllers\Actions;

class ProductVariantsController extends Controller
{
    use Actions\FetchMany;
    use Actions\FetchRelated;
    use Actions\FetchRelationship;
}
