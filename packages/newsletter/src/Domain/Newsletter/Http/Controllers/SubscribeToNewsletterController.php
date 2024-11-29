<?php

namespace Dystcz\LunarNewsletter\Domain\Newsletter\Http\Controllers;

use Dystcz\LunarApi\Controller;
use Dystcz\LunarNewsletter\Domain\Newsletter\Actions\SubscribeToNewsletter;
use Dystcz\LunarNewsletter\Domain\Newsletter\JsonApi\V1\NewsletterSubscriptionRequest;
use Illuminate\Http\Response;

class SubscribeToNewsletterController extends Controller
{
    /**
     * Subscribe to newsletter.
     */
    public function subscribe(
        NewsletterSubscriptionRequest $request,
        SubscribeToNewsletter $subscribeToNewsletter
    ): Response {
        $subscribeToNewsletter($request->input('data.attributes.email'));

        return response('', 201);
    }
}
