<?php

use Dystore\Api\Domain\Carts\Models\Cart;
use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('cannot list cart lines without cart', function () {
    /** @var TestCase $this */
    $cart = Cart::factory()->withLines()->create();

    $response = $this
        ->jsonApi()
        ->expects('cart_lines')
        ->get(serverUrl('/cart_lines'));

    $response->assertErrorStatus([
        'status' => '405',
        'title' => 'Method Not Allowed',
    ]);

})->group('cart_lines', 'policies');
