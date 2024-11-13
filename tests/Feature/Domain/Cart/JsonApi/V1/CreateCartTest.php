<?php

use Dystore\Api\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Lunar\Base\CartSessionInterface;

uses(TestCase::class, RefreshDatabase::class);

it('can automatically create cart when configured', function () {
    /** @var TestCase $this */
    Config::set('lunar.cart.auto_create', true);

    $response = $this
        ->jsonApi()
        ->expects('carts')
        ->get(serverUrl('/carts/-actions/my-cart'));

    /** @var \Lunar\Managers\CartSessionManager $cartSession */
    $cartSession = App::make(CartSessionInterface::class);

    $cart = $cartSession->current();

    $response
        ->assertSuccessful()
        ->assertFetchedOne($cart);

    $cartSession->forget();

})->group('carts', 'carts.create');

it('does not automatically create cart when configured', function () {
    /** @var TestCase $this */
    Config::set('lunar.cart_session.auto_create', false);

    $response = $this
        ->jsonApi()
        ->expects('carts')
        ->get(serverUrl('/carts/-actions/my-cart'));

    $response
        ->assertSuccessful()
        ->assertFetchedNull();

})->group('carts', 'carts.create');

test('cart models cannot be explicitely created', function () {
    /** @var TestCase $this */
    $response = $this->createTest('carts', []);

    $response->assertErrorStatus([
        'status' => '404',
        'title' => 'Not Found',
    ]);
})->group('carts', 'policies', 'carts.create');
