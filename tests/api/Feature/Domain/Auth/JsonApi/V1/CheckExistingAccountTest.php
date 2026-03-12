<?php

use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Testing\Fakes\EventFake;

use function Pest\Faker\fake;

uses(TestCase::class, RefreshDatabase::class)
    ->group('auth', 'users');

test('can check if account exists by providing an email', function () {
    /** @var TestCase $this */

    /** @var EventFake $eventFake */
    $event = Event::fake();

    $data = [
        'type' => 'auth',
        'attributes' => [
            'email' => $email = fake()->safeEmail(),
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('users')
        ->withData($data)
        ->post(serverUrl('/auth/-actions/check-existing-account'));

    $this->assertFalse($response->json('exists'));

    $user = User::factory()->create([
        'email' => $email = fake()->safeEmail(),
    ]);

    $data = [
        'type' => 'auth',
        'attributes' => [
            'email' => $user->email,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('users')
        ->withData($data)
        ->post(serverUrl('/auth/-actions/check-existing-account'));

    $this->assertTrue($response->json('exists'));
});
