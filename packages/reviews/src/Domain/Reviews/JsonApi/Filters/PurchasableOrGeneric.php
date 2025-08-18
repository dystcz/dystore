<?php

namespace Dystore\Reviews\Domain\Reviews\JsonApi\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use LaravelJsonApi\Eloquent\Contracts\Filter;
use Throwable;

/** @phpstan-consistent-constructor */
class PurchasableOrGeneric implements Filter
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): self
    {
        return new static($name);
    }

    public function key(): string
    {
        return $this->name;
    }

    public function isSingular(): bool
    {
        return false;
    }

    public function apply($query, $value): Builder
    {
        if (! is_string($value) || ! Str::contains($value, [':', ','])) {
            return $query;
        }

        $delimiter = Str::contains($value, ':') ? ':' : ',';
        [$type, $id] = array_pad(explode($delimiter, $value, 2), 2, null);

        $type = (string) $type;
        $id = (string) $id;

        // Normalize provided type to morph type keys
        $morphType = match ($type) {
            'products', 'product' => 'product',
            'product_variants', 'product_variant' => 'product_variant',
            default => null,
        };

        if (empty($morphType) || empty($id)) {
            return $query;
        }

        // Resolve class from morph map if available
        $class = Relation::getMorphedModel($morphType);

        // Resolve route key to primary key using resolved class (handles hashed IDs)
        $resolvedKey = null;
        if ($class && class_exists($class)) {
            try {
                $model = new $class;
                $bound = $model->resolveRouteBinding($id);
                if ($bound) {
                    $resolvedKey = $bound->getKey();
                }
            } catch (Throwable $e) {
                // ignore
            }
        }
        $targetKey = $resolvedKey ?? $id;

        return $query->where(function ($q) use ($morphType, $targetKey) {
            $q->where('purchasable_type', '=', null)
                ->orWhere(function ($q) use ($morphType, $targetKey) {
                    $q->whereHasMorph('purchasable', [$morphType], function ($q) use ($targetKey) {
                        $q->whereKey($targetKey);
                    });
                });
        });
    }
}
