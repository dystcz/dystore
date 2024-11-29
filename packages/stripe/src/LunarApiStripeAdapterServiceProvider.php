<?php

namespace Dystcz\LunarApiStripeAdapter;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LunarApiStripeAdapterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name('lunar-api-stripe-adapter')->hasConfigFile();
    }

    public function packageRegistered()
    {
        StripePaymentAdapter::register();
    }
}
