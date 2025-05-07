<?php

use Dystore\Newsletter\Drivers\EcomailDriver;
use Dystore\Tests\Newsletter\TestCase;
use Illuminate\Support\Facades\Config;
use Spatie\Newsletter\Facades\Newsletter;

uses(TestCase::class)
    ->group('newsletter', 'ecomail');

it('can get the Ecomail API', function () {
    /** @var TestCase $this */
    Config::set('newsletter.driver', EcomailDriver::class);

    expect(Newsletter::getApi())->toBeInstanceOf(Ecomail::class);
})->skip(fn () => ! class_exists(Ecomail::class), 'Ecomail is not installed.');
