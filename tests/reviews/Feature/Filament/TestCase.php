<?php

namespace Dystore\Tests\Reviews\Feature\Filament;

use Dystore\Reviews\Domain\Reviews\Filament\Resources\Review\ReviewResource;
use Dystore\Tests\Reviews\TestCase as BaseTestCase;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentAsset;
use Filament\View\LegacyComponents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootFilament();
        $this->setupFilamentPanel();
    }

    protected function bootFilament(): void
    {
        Blade::components([
            LegacyComponents\Page::class => 'filament::page',
            LegacyComponents\Widget::class => 'filament::widget',
        ]);

        $vendorPath = dirname(__DIR__, 4).'/vendor';

        FilamentAsset::register([
            \Filament\Support\Assets\Js::make('app', "{$vendorPath}/filament/filament/dist/index.js")->core(),
            \Filament\Support\Assets\Js::make('echo', "{$vendorPath}/filament/filament/dist/echo.js")->core(),
            \Filament\Support\Assets\Theme::make('app', "{$vendorPath}/filament/filament/dist/theme.css"),
        ], 'filament/filament');

        Filament::serving(function () {
            Filament::setServingStatus();
        });
    }

    protected function setupFilamentPanel(): void
    {
        $panel = $this->getTestPanel();

        Filament::registerPanel($panel);

        Filament::setCurrentPanel($panel);

        $this->actingAs($this->getTestUser());
    }

    protected function getTestPanel(): Panel
    {
        return Panel::make()
            ->id('test')
            ->path('test')
            ->resources([
                ReviewResource::class,
            ]);
    }

    protected function getTestUser()
    {
        $userClass = config('auth.providers.users.model');

        return $userClass::factory()->create();
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();

        $this->loadMigrationsFrom(base_path('packages/reviews/database/migrations'));

        $this->artisan('migrate', ['--force' => true])->run();
    }
}
