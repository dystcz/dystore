<?php

use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('users');

beforeEach(function () {
    /** @var TestCase $this */
    $this->user = User::factory()->create();
});

it('can delete own account', function () {
    /** @var TestCase $this */
    $id = $this->user->getRouteKey();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('users')
        ->delete(serverUrl("users/{$id}"));

    $response
        ->assertSuccessful()
        ->assertNoContent();

    $this->assertDatabaseMissing('users', [
        'id' => $this->user->getKey(),
    ]);
})->group('users');

it('cannot delete another user account', function () {
    /** @var TestCase $this */
    $otherUser = User::factory()->create();

    $id = $otherUser->getRouteKey();

    $response = $this
        ->actingAs($this->user)
        ->jsonApi()
        ->expects('users')
        ->delete(serverUrl("users/{$id}"));

    $response->assertErrorStatus([
        'detail' => 'This action is unauthorized.',
        'status' => '403',
        'title' => 'Forbidden',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $otherUser->getKey(),
    ]);
})->group('users', 'policies');

it('cannot delete user when unauthenticated', function () {
    /** @var TestCase $this */
    $id = $this->user->getRouteKey();

    $response = $this
        ->jsonApi()
        ->expects('users')
        ->delete(serverUrl("users/{$id}"));

    $response->assertErrorStatus([
        'detail' => 'This action is unauthorized.',
        'status' => '403',
        'title' => 'Forbidden',
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->getKey(),
    ]);
})->group('users', 'policies');
