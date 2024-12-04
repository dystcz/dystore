<?php

use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
    $this->user = User::factory()
        ->has(Customer::factory())
        ->create();

    $this->customer = $this->user->customers->first();
});

it('can read a customer when logged in', function () {
    /** @var TestCase $this */
    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('customers')
        ->get(serverUrl("/customers/{$this->customer->getRouteKey()}"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($this->customer);

})->group('customers');

it('cannot read customer when not logged in', function () {
    /** @var TestCase $this */
    $response = $this
        ->jsonApi()
        ->expects('customers')
        ->get(serverUrl("/customers/{$this->customer->getRouteKey()}"));

    $response->assertErrorStatus([
        'detail' => 'Unauthenticated.',
        'status' => '401',
        'title' => 'Unauthorized',
    ]);

})->group('customers');
