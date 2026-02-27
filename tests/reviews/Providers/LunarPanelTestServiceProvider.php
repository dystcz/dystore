<?php

namespace Dystore\Tests\Reviews\Providers;

use Dystore\Reviews\Domain\Reviews\Filament\Plugins\ReviewsPlugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Support\Facades\LunarPanel;

class LunarPanelTestServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Lunar\Admin\Support\Facades\LunarPanel::register();

        LunarPanel::panel(
            fn (Panel $panel) => $panel
                ->plugins([
                    ReviewsPlugin::make(),
                ])
        )->register();
    }
}
