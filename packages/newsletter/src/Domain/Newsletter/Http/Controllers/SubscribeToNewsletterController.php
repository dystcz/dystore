<?php

namespace Dystcz\LunarApiNewsletter\Domain\Newsletter\Http\Controllers;

use Dystcz\LunarApi\Base\Controller;
use Dystcz\LunarApiNewsletter\Domain\Newsletter\Actions\SubscribeToNewsletter;
use Dystcz\LunarApiNewsletter\Domain\Newsletter\JsonApi\V1\NewsletterSubscriptionRequest;
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
