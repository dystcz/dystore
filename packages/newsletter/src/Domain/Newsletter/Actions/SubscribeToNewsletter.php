<?php

namespace Dystore\Newsletter\Domain\Newsletter\Actions;

use Dystore\Api\Support\Actions\Action;
use Dystore\Newsletter\Domain\Newsletter\Events\NewsletterSubscribed;
use Spatie\Newsletter\Facades\Newsletter;

class SubscribeToNewsletter extends Action
{
    /**
     * Subscribe to newsletter.
     */
    public function handle(string $email): void
    {
        Newsletter::subscribe($email);
        NewsletterSubscribed::dispatch($email);
    }
}
