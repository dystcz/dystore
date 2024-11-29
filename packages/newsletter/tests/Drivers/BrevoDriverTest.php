<?php

use Brevo\Client\Api\ContactsApi as Brevo;
use Dystcz\LunarApiNewsletter\Drivers\BrevoDriver;
use Dystcz\LunarApiNewsletter\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Spatie\Newsletter\Facades\Newsletter;

uses(TestCase::class);

it('can get the Brevo API', function () {
    Config::set('newsletter.driver', BrevoDriver::class);

    expect(Newsletter::getApi())->toBeInstanceOf(Brevo::class);
});
