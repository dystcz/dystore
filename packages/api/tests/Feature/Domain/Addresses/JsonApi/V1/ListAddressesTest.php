<?php

use Dystore\Api\Domain\Addresses\Models\Address;
use Dystore\Api\Domain\Countries\Models\Country;
use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
    $this->user = User::factory()
        ->has(Customer::factory())
        ->create();
});

it('can list addresses', function () {
    /** @var TestCase $this */
    $models = Address::factory()
        ->count(2)
        ->create([
            'customer_id' => $this->user->customers->first()->getKey(),
        ]);

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('addresses')
        ->get('/api/v1/addresses');

    $response
        ->assertSuccessful()
        ->assertFetchedMany($models);

})->group('addresses');

test('user cannot list addresses belonging to other users', function () {
    /** @var TestCase $this */
    $models = Address::factory()
        ->count(3)
        ->create([
            'customer_id' => User::factory()->has(Customer::factory()),
        ]);

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('addresses')
        ->get('/api/v1/addresses');

    $response
        ->assertSuccessful()
        ->assertFetchedNone();

})->group('addresses', 'policies');

it('can list addresses with country included', function () {
    /** @var TestCase $this */
    $country = Country::factory()
        ->create();

    $models = Address::factory()
        ->count(2)
        ->create([
            'country_id' => $country->id,
            'customer_id' => $this->user->customers->first()->getKey(),
        ]);

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->includePaths('country')
        ->expects('addresses')
        ->get('/api/v1/addresses');

    $response
        ->assertSuccessful()
        ->assertFetchedMany($models)
        ->assertIncluded(
            mapModelsToResponseData('countries', $models->pluck('country')),
        );

})->group('addresses');

it('can list addresses with customer included', function () {

    /** @var TestCase $this */
    $models = Address::factory()
        ->count(2)
        ->create([
            'customer_id' => $this->user->customers->first()->getKey(),
        ]);

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->includePaths('customer')
        ->expects('addresses')
        ->get('/api/v1/addresses');

    $response
        ->assertSuccessful()
        ->assertFetchedMany($models)
        ->assertIncluded(
            mapModelsToResponseData('customers', $models->pluck('customer')),
        );

})->group('addresses');

it('can list addresses with countries and customers included', function () {
    /** @var TestCase $this */
    $models = Address::factory()
        ->count(3)
        ->create([
            'customer_id' => $this->user->customers->first()->getKey(),
        ]);

    /** @var TestCase $this */
    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('addresses')
        ->includePaths('country', 'customer')
        ->get(serverUrl('/addresses'));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($models)
        ->assertIncluded([
            ...mapModelsToResponseData('countries', $models->pluck('country')),
            ...mapModelsToResponseData('customers', $models->pluck('customer')),
        ]);

})->group('addresses');
