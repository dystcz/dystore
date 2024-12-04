<?php

use Dystore\Api\Domain\Addresses\Models\Address;
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
});

it('can delete an address', function () {

    /** @var TestCase $this */
    $model = Address::factory()
        ->create([
            'customer_id' => $this->user->customers->first()->getKey(),
        ]);

    $id = $model->getRouteKey();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('addresses')
        ->delete(serverUrl("addresses/{$id}"));

    $response
        ->assertSuccessful()
        ->assertNoContent();

    $this->assertDatabaseMissing($model->getTable(), [
        'id' => $id,
    ]);

})->group('addresses');

it('can delete only address belonging to logged in user', function () {
    /** @var TestCase $this */
    $user2 = User::factory()
        ->has(Customer::factory())
        ->create();

    $model = Address::factory()
        ->create([
            'customer_id' => $user2->customers->first()->getKey(),
        ]);

    $id = $model->getRouteKey();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('addresses')
        ->delete(serverUrl("addresses/{$id}"));

    $response->assertErrorStatus([
        'detail' => 'This action is unauthorized.',
        'status' => '403',
        'title' => 'Forbidden',
    ]);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $id,
    ]);

})->group('addresses', 'policies');
