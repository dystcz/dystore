<?php

use Dystore\Api\Domain\Users\Contracts\RegistersUser;
use Dystore\Api\Domain\Users\Data\UserData;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Testing\Fakes\EventFake;

uses(TestCase::class, RefreshDatabase::class)
    ->group('auth', 'users');

it('can register a user', function () {
    /** @var TestCase $this */
    $user = User::factory()->make();

    /** @var EventFake $eventFake */
    $eventFake = Event::fake();

    $data = [
        'type' => 'users',
        'attributes' => [
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'accept_terms' => true,
        ],
    ];

    $response = $this
        ->jsonApi()
        ->expects('users')
        ->withData($data)
        ->post('/api/v1/users');

    $id = $response
        ->assertCreatedWithServerId(
            Config::get('app.url').'/api/v1/users',
            Arr::only($data, ['email'])
        )
        ->id();

    $eventFake->assertDispatched(
        Registered::class,
        fn (Registered $event) => $event->user->email === $user->email,
    );

    $this->assertDatabaseHas((new User)->getTable(), [
        'id' => $id,
        'email' => $user->email,
        'password_set' => true,
    ]);
});

it('returns existing user when registering with existing email', function () {
    /** @var TestCase $this */
    $existingUser = User::factory()->create();

    /** @var EventFake $eventFake */
    $eventFake = Event::fake();

    /** @var RegistersUser $registerUser */
    $registerUser = $this->app->make(RegistersUser::class);

    $user = $registerUser->register(new UserData(
        email: $existingUser->email,
    ));

    expect($user->getKey())->toBe($existingUser->getKey());
    expect($user->email)->toBe($existingUser->email);

    $eventFake->assertNotDispatched(Registered::class);

    $this->assertDatabaseCount('users', 1);
});
