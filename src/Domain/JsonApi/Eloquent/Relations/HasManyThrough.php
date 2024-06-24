<?php

namespace Dystcz\LunarApi\Domain\JsonApi\Eloquent\Relations;

use Dystcz\LunarApi\Support\Typescript\Types\RelationType;
use LaravelJsonApi\Eloquent\Fields\Relations\HasManyThrough as LaravelJsonApiHasManyThrough;
use Spatie\TypeScriptTransformer\Attributes\RecordTypeScriptType;

#[RecordTypeScriptType(keyType: 'string', valueType: RelationType::class)]
class HasManyThrough extends LaravelJsonApiHasManyThrough {}
