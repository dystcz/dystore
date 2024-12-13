<?php

use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses(TestCase::class, RefreshDatabase::class)
    ->group('auth', 'logout');

it('can log out a user', function () {
    /** @var TestCase $this */
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->post(serverUrl('/auth/-actions/logout'));

    $response->assertSuccessful();

    expect(Auth::check())->toBe(false);
});
