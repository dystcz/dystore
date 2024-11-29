<?php

namespace Dystcz\LunarApiNewsletter\Domain\Newsletter\Events;

use Illuminate\Foundation\Events\Dispatchable;

class NewsletterSubscribed
{
    use Dispatchable;

    public function __construct(
        public string $email,
    ) {}
}
