<?php

use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\OrderLines\Models\OrderLine;
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

test('users cannot update order lines', function () {
    /** @var TestCase $this */
    $response = $this->updateTest('order_lines', OrderLine::class, []);

    $response->assertErrorStatus([
        'status' => '404',
        'title' => 'Not Found',
    ]);
})->group('order_lines', 'policies');
