<?php

namespace Dystcz\LunarApiNewsletter\Domain\Newsletter\Actions;

use Spatie\Newsletter\Facades\Newsletter;

class SubscribeToNewsletter
{
    /**
     * Subscribe to newsletter.
     */
    public function __invoke(string $email): void
    {
        Newsletter::subscribe($email);
    }
}
