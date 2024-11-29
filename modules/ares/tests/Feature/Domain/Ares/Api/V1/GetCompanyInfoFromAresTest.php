<?php

use Dystcz\LunarApi\Support\Models\Actions\SchemaType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\DystoreAres\Domain\Ares\Entities\AresRecord;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)
    ->group('ares');

it('can get company info from ares with provided company id', function () {
    /** @var TestCase $this */
    $type = SchemaType::get(AresRecord::class);

    $response = $this
        ->jsonApi()
        ->expects('ares-records')
        ->withData([
            'type' => $type,
            'attributes' => [
                'company_in' => '05817226',
            ],
        ])
        ->post(serverUrl("/{$type}/-actions/get-company-info"));

    $response
        ->assertSuccessful();
    // ->assertFetchedOne()
    // ->assertDoesntHaveIncluded();
});
