<?php

use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\Tags\Models\Tag;
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

test('tags cannot be updated', function () {
    /** @var TestCase $this */
    $response = $this->updateTest('tags', Tag::class, []);

    $response->assertErrorStatus([
        'status' => '405',
        'title' => 'Method Not Allowed',
    ]);
})->group('tags', 'policies');
