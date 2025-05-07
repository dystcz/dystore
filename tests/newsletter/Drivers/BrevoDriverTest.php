<?php

use Brevo\Client\Api\ContactsApi as Brevo;
use Dystore\Newsletter\Drivers\BrevoDriver;
use Dystore\Tests\Newsletter\TestCase;
use Illuminate\Support\Facades\Config;
use Spatie\Newsletter\Facades\Newsletter;

uses(TestCase::class)
    ->group('newsletter', 'brevo');

it('can get the Brevo API', function () {
    Config::set('newsletter.driver', BrevoDriver::class);

    expect(Newsletter::getApi())->toBeInstanceOf(Brevo::class);
});
