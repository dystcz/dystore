<?php

use Dystore\Tests\Newsletter\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Newsletter\Facades\Newsletter;

use function Pest\Faker\fake;

uses(TestCase::class, RefreshDatabase::class);

it('can subscribe user to newsletter', function () {
    /** @var TestCase $this */
    $email = fake()->email();

    Newsletter::shouldReceive('subscribe')
        ->once()
        ->with($email);

    $data = [
        'type' => 'newsletters',
        'attributes' => [
            'email' => $email,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('newsletters')
        ->withData($data)
        ->post('/api/v1/newsletters/-actions/subscribe');

    $response->assertSuccessful();
});

it('requires emails in order to sign up', function () {
    /** @var TestCase $this */
    $data = [
        'type' => 'newsletters',
        'attributes' => [
            'email' => '',
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('newsletters')
        ->withData($data)
        ->post('/api/v1/newsletters/-actions/subscribe');

    $expected = [
        'source' => ['pointer' => '/data/attributes/email'],
        'status' => '422',
        'detail' => 'Please enter an email address',
    ];

    $response->assertError(422, $expected);
});

it('doesnt accept invalid emails', function () {
    /** @var TestCase $this */
    $data = [
        'type' => 'newsletters',
        'attributes' => [
            'email' => 'ahoj',
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('newsletters')
        ->withData($data)
        ->post('/api/v1/newsletters/-actions/subscribe');

    $expected = [
        'source' => ['pointer' => '/data/attributes/email'],
        'status' => '422',
        'detail' => 'Please enter a valid email address',
    ];

    $response->assertError(422, $expected);
});
