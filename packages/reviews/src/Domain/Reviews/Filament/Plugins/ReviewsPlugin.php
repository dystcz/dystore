<?php

namespace Dystore\Reviews\Domain\Reviews\Filament\Plugins;

use Dystore\Reviews\Domain\Reviews\Filament\Resources\ReviewResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ReviewsPlugin implements Plugin
{
    final public function __construct() {}

    public static function make(): self
    {
        return new static;
    }

    public function getId(): string
    {
        return 'dystore-reviews';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ReviewResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
