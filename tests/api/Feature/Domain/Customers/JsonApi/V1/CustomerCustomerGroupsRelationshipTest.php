<?php

use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can list customer groups through relationship', function () {
    /** @var TestCase $this */
    $customer = Customer::factory()
        ->withCustomerGroup()
        ->has(User::factory())
        ->create();

    $this->actingAs($customer->users->first());

    $response = $this
        ->jsonApi()
        ->expects('customer_groups')
        ->get(serverUrl("/customers/{$customer->getRouteKey()}/customer_groups"));

    $response
        ->assertSuccessful()
        ->assertFetchedMany($customer->customerGroups)
        ->assertDoesntHaveIncluded();
})->group('customers');

it('can count customer groups', function () {
    /** @var TestCase $this */
    $customer = Customer::factory()
        ->withCustomerGroup()
        ->has(User::factory())
        ->create();

    $this->actingAs($customer->users->first());

    $response = $this
        ->jsonApi()
        ->expects('customers')
        ->get(serverUrl("/customers/{$customer->getRouteKey()}?with_count=customer_groups"));

    $response
        ->assertSuccessful()
        ->assertFetchedOne($customer);

    expect($response->json('data.relationships.customer_groups.meta.count'))->toBe(1);
})->group('customers');
