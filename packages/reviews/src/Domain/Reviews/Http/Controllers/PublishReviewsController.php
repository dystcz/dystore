<?php

namespace Dystore\Reviews\Domain\Reviews\Http\Controllers;

use Dystore\Api\Base\Controller;
use Dystore\Reviews\Domain\Reviews\JsonApi\V1\ReviewSchema;
use Dystore\Reviews\Domain\Reviews\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LaravelJsonApi\Core\Responses\DataResponse;

class PublishReviewsController extends Controller
{
    public function publish(
        ReviewSchema $schema,
        Request $query,
        Review $review
    ): DataResponse {
        $this->authorize('publish', $review);

        abort_if($review->published_at, 403, 'Review is already published.');

        $review->update(['published_at' => now()]);

        $model = $schema
            ->repository()
            ->queryOne($review)
            ->withRequest($query)
            ->first();

        return new DataResponse($model);
    }

    public function unpublish(
        ReviewSchema $schema,
        Request $query,
        Review $review
    ): Response {
        $this->authorize('unpublish', $review);

        abort_if(! $review->published_at, 403, 'Review is already unpublished.');

        $review->update(['published_at' => null]);

        return response('', 204);
    }
}
