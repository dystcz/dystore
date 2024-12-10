<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\Currency;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can update storefront session currency', function () {
    /** @var TestCase $this */

    /** @var Currency $currency */
    $currency = Currency::factory()->create([
        'default' => false,
    ]);

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->includePaths(
            'currency',
        )
        ->get(serverUrl('/storefronts/lunar_storefront'));

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    $response->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'storefronts',
            'id' => $storefrontSession->getSessionKey(),
        ]);

    $currencyId = $response->json('data.relationships.currency.data.id');

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->withData([
            'type' => 'currencies',
            'id' => (string) $currency->getRouteKey(),
        ])
        ->patch(serverUrl('/storefronts/lunar_storefront/relationships/currency'));

    $newCurrencyId = $response->json('data.id');

    $response->assertSuccessful();

    expect($currencyId)->not->toBe($newCurrencyId);
});
