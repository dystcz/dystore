<?php

namespace Dystcz\LunarApiNewsletter\Domain\Newsletter\Actions;

use Dystcz\LunarApi\Support\Actions\Action;
use Spatie\Newsletter\Facades\Newsletter;

class SubscribeToNewsletter extends Action
{
    /**
     * Subscribe to newsletter.
     */
    public function handle(string $email): void
    {
        Newsletter::subscribe($email);
    }
}
