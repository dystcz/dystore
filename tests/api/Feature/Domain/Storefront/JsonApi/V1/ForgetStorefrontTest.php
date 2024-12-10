<?php

use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Lunar\Base\StorefrontSessionInterface;
use Lunar\Managers\StorefrontSessionManager;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\Customer;
use Lunar\Models\CustomerGroup;

uses(TestCase::class, RefreshDatabase::class)
    ->group('storefront');

it('can forget storefront session', function () {
    /** @var TestCase $this */

    /** @var Currency $currency */
    $currency = Currency::factory()->create([
        'default' => false,
    ]);

    /** @var Channel $channel */
    $channel = Channel::factory()->create([
        'default' => false,
    ]);

    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    /** @var Collection $customerGroups */
    $customerGroups = CustomerGroup::factory()->count(3)->create();

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    $storefrontSession->setCurrency($currency);
    $storefrontSession->setChannel($channel);
    $storefrontSession->setCustomer($customer);
    $storefrontSession->setCustomerGroups($customerGroups);

    ray($storefrontSession);

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->includePaths(
            'currency',
            'customer',
            'channel',
            'customer_groups',
        )
        ->get(serverUrl('/storefronts/lunar_storefront'));

    $response->assertSuccessful()
        ->assertFetchedOne([
            'type' => 'storefronts',
            'id' => $storefrontSession->getSessionKey(),
        ])
        ->assertIncluded([
            [
                'type' => 'currencies',
                'id' => $currency->getKey(),
            ],
            [
                'type' => 'channels',
                'id' => $channel->getKey(),
            ],
            [
                'type' => 'customers',
                'id' => $customer->getKey(),
            ],
            ...$customerGroups->map(function (CustomerGroup $customerGroup) {
                return [
                    'type' => 'customer_groups',
                    'id' => $customerGroup->getKey(),
                ];
            })->all(),
        ]);

    $response = $this
        ->jsonApi()
        ->expects('storefronts')
        ->delete(serverUrl('/storefronts/lunar_storefront'));

    $response
        ->assertSuccessful();

    /** @var StorefrontSessionManager $storefrontSession */
    $storefrontSession = App::make(StorefrontSessionInterface::class);

    expect($storefrontSession->getChannel())->not->toBe($channel);
    expect($storefrontSession->getCustomer())->toBeNull();
    expect($storefrontSession->getCustomerGroups()->all())->toBe([]);
});
