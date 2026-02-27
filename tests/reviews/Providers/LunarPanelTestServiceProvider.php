<?php

namespace Dystore\Tests\Reviews\Providers;

use Illuminate\Support\ServiceProvider;

class LunarPanelTestServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Lunar\Admin\Support\Facades\LunarPanel::register();
    }
}
