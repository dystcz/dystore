<?php

use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(TestCase::class, RefreshDatabase::class)
    ->group('auth');

it('can get currently logged in user', function () {
    /** @var TestCase $this */
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('users')
        ->get(serverUrl('/auth/-actions/me'));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($user);
});

it('can get currently logged in user with customers included', function () {
    /** @var TestCase $this */
    $user = User::factory()
        ->has(Customer::factory(), 'customers')
        ->create([
            'password' => Hash::make('password'),
        ]);

    $response = $this
        ->actingAs($user)
        ->jsonApi()
        ->expects('users')
        ->includePaths('customers')
        ->get(serverUrl('/auth/-actions/me'));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($user)
        ->assertIsIncluded('customers', $user->customers->first());
});
