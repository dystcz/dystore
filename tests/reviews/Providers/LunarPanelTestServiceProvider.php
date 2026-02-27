<?php

namespace Dystore\Tests\Reviews\Providers;

use Dystore\Reviews\Domain\Reviews\Filament\Resources\Review\ReviewResource;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;

class LunarPanelTestServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Lunar\Admin\Support\Facades\LunarPanel::register();

        Panel::configureUsing(fn (Panel $panel) => $panel
            ->resources([
                ReviewResource::class,
            ])
        );
    }
}
