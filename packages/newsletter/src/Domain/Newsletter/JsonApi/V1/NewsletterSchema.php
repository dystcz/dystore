<?php

namespace Dystcz\LunarNewsletter\Domain\Newsletter\JsonApi\V1;

use Dystcz\LunarNewsletter\Domain\Newsletter\Entities\NewsletterSubscription;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Schema;

class NewsletterSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     */
    public static string $model = NewsletterSubscription::class;

    /**
     * Get the resource fields.
     */
    public function fields(): array
    {
        return [
            Str::make('email'),
        ];
    }

    /**
     * Get the JSON:API resource type.
     */
    public static function type(): string
    {
        return 'newsletters';
    }

    /**
     * Determine if the resource is authorizable.
     */
    public function authorizable(): bool
    {
        return false;
    }
}
