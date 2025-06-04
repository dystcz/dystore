<?php

use Dystore\Api\Base\Facades\JsonApiManifest;
use Dystore\Tests\Api\Feature\Domain\JsonApi\Base\ServerMock;
use Dystore\Tests\Api\Feature\Domain\JsonApi\Stubs\EloquentSchemaMock;
use Dystore\Tests\Api\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;

uses(TestCase::class, RefreshDatabase::class)
    ->group('manifests', 'extending');

beforeEach(function () {
    /** @var TestCase $this */
    $this->server = App::make(ServerMock::class, ['name' => 'v1']);

    JsonApiManifest::addSchema(EloquentSchemaMock::class);
});

test('schema eager loading can be extended', function () {
    /** @var TestCase $this */
    $mockSchemaInstance = $this->server->schemas()->schemaFor('tags');

    expect($mockSchemaInstance)
        ->with()
        ->not
        ->toContain('else')
        ->toHaveCount(1);

    JsonApiManifest::schema(EloquentSchemaMock::class)
        ->addWith([
            'something',
            'else',
            'else',
            'other',
        ])
        ->removeWith('else');

    expect(JsonApiManifest::schema(EloquentSchemaMock::class))
        ->with()
        ->resolve()
        ->toContain('something', 'other');

    expect($mockSchemaInstance)
        ->with()
        ->toBe([
            'something',
            'other',
        ]);
});

test('schema fields can be extended', function () {
    /** @var TestCase $this */
    $mockSchemaInstance = $this->server->schemas()->schemaFor('tags');

    $field = Str::make('nazdar');

    expect(iterator_to_array($mockSchemaInstance->fields()))
        ->not
        ->toContain($field)
        ->toHaveCount(2);

    JsonApiManifest::schema(EloquentSchemaMock::class)
        ->addFields($field);

    expect(JsonApiManifest::schema(EloquentSchemaMock::class))
        ->fields()
        ->toContain($field)
        ->toHaveCount(3);

    expect(iterator_to_array($mockSchemaInstance->fields()))
        ->toContainEqual($field)
        ->toHaveCount(3);
});

test('schema filters can be extended', function () {
    /** @var TestCase $this */
    $mockSchemaInstance = $this->server->schemas()->schemaFor('tags');

    $filter = Where::make('new_filter');

    expect($mockSchemaInstance)
        ->filters()
        ->not
        ->toContain($filter)
        ->toHaveCount(3);

    JsonApiManifest::schema(EloquentSchemaMock::class)
        ->addFilters([
            $filter,
        ]);

    expect(JsonApiManifest::schema(EloquentSchemaMock::class))
        ->filters()
        ->toContain($filter)
        ->toHaveCount(2);

    expect($mockSchemaInstance)
        ->filters()
        ->toContainEqual($filter)
        ->toHaveCount(4);
});

test('schema sortables can be extended', function () {
    /** @var TestCase $this */
    $mockSchemaInstance = $this->server->schemas()->schemaFor('tags');

    $sortables = ['nazdar', 'cau'];

    expect(iterator_to_array($mockSchemaInstance->sortables()))
        ->not
        ->toContain('nazdar', 'cau')
        ->toHaveCount(1);

    JsonApiManifest::schema(EloquentSchemaMock::class)
        ->addSortables($sortables);

    expect(JsonApiManifest::schema(EloquentSchemaMock::class))
        ->sortables()
        ->toContain('ahoj', 'nazdar', 'cau')
        ->toHaveCount(3);

    expect(iterator_to_array($mockSchemaInstance->sortables()))
        ->toContain('ahoj', 'nazdar', 'cau')
        ->toHaveCount(3);
});

test('schema related gate ability can be extended', function () {
    /** @var TestCase $this */
    $mockSchemaInstance = $this->server->schemas()->schemaFor('tags');

    $related = ['two', 'three', 'four', 'one'];

    expect($mockSchemaInstance)
        ->showRelated()
        ->not
        ->toContain('two', 'three', 'four')
        ->toHaveCount(1)
        ->toContain('one');

    JsonApiManifest::schema(EloquentSchemaMock::class)
        ->addShowRelated(
            $related
        );

    expect(JsonApiManifest::schema(EloquentSchemaMock::class))
        ->showRelated()
        ->toContain('two', 'three', 'four', 'one')
        ->toHaveCount(4);

    expect($mockSchemaInstance)
        ->showRelated()
        ->toContain('two', 'three', 'four', 'one')
        ->toHaveCount(4);
});

test('schema relationships gate ability can be extended', function () {
    /** @var TestCase $this */
    $mockSchemaInstance = $this->server->schemas()->schemaFor('tags');

    $relationships = ['pear', 'peach'];

    expect($mockSchemaInstance)
        ->showRelationships()
        ->not
        ->toContain('pear', 'peach')
        ->toHaveCount(1);

    JsonApiManifest::schema(EloquentSchemaMock::class)
        ->setShowRelationships(
            $relationships
        );

    expect(JsonApiManifest::schema(EloquentSchemaMock::class))
        ->showRelationships()
        ->toContain('pear', 'peach')
        ->toHaveCount(2);

    expect($mockSchemaInstance)
        ->showRelationships()
        ->toContain('apple', 'pear', 'peach')
        ->toHaveCount(3);
});
