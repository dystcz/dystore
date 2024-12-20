<?php

use Dystore\Api\Domain\Brands\Models\Brand;
use Dystore\Api\Domain\Customers\Models\Customer;
use Dystore\Api\Domain\Media\Factories\MediaFactory;
use Dystore\Api\Domain\Users\Models\User;
use Dystore\Tests\Api\Data\TestInclude;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    /** @var TestCase $this */
    $this->user = User::factory()
        ->has(Customer::factory())
        ->create();
});

it('can list all brands', function () {
    /** @var TestCase $this */
    $response = $this->indexTest('brands', Brand::class, 20);
})->group('brands');

it('can list brands with custom pagination', function () {
    /** @var TestCase $this */
    $response = $this->paginationTest('brands', Brand::class);

})->group('brands');

test('brands can be listed with thumbnail included', function () {

    /** @var TestCase $this */
    $includes = Collection::make([
        'thumbnail' => new TestInclude(
            factory: MediaFactory::new()->thumbnail()->count(1),
            factory_relation: 'media',
            type: 'media',
            relation: 'media',
            factory_relation_method: 'has',
        ),
    ]);

    $response = $this->indexWithIncludesTest('brands', Brand::class, 5, $includes);

})->group('brands');

test('brands cannot be deleted', function () {
    /** @var TestCase $this */
    $response = $this->deleteTest('brands', Brand::class);

    $response->assertErrorStatus([
        'status' => '405',
        'title' => 'Method Not Allowed',
    ]);
})->group('brands', 'policies');
