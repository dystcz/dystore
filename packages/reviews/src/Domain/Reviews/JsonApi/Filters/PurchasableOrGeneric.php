<?php

namespace Dystore\Reviews\Domain\Reviews\JsonApi\Filters;

use Illuminate\Support\Str;
use LaravelJsonApi\Eloquent\Contracts\Filter;

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

    public function apply($query, $value)
    {
        if (! is_string($value) || ! Str::contains($value, [':', ','])) {
            return $query;
        }

        $delimiter = Str::contains($value, ':') ? ':' : ',';
        [$type, $id] = array_pad(explode($delimiter, $value, 2), 2, null);

        $type = (string) $type;
        $id = (string) $id;

        $classes = match ($type) {
            'products' => array_values(array_filter([
                class_exists('Lunar\\Models\\Product') ? 'Lunar\\Models\\Product' : null,
                class_exists('Dystore\\Api\\Domain\\Products\\Models\\Product') ? 'Dystore\\Api\\Domain\\Products\\Models\\Product' : null,
            ])),
            'product_variants' => array_values(array_filter([
                class_exists('Lunar\\Models\\ProductVariant') ? 'Lunar\\Models\\ProductVariant' : null,
                class_exists('Dystore\\Api\\Domain\\ProductVariants\\Models\\ProductVariant') ? 'Dystore\\Api\\Domain\\ProductVariants\\Models\\ProductVariant' : null,
            ])),
            default => [],
        };

        if (empty($classes) || empty($id)) {
            return $query;
        }

        // Resolve route key to primary key using available classes
        $resolvedKey = null;
        foreach ($classes as $class) {
            try {
                $model = new $class;
                $bound = $model->resolveRouteBinding($id);
                if ($bound) {
                    $resolvedKey = $bound->getKey();
                    break;
                }
            } catch (\Throwable $e) {
                // ignore and try next
            }
        }
        $targetKey = $resolvedKey ?? $id;

        return $query->where(function ($q) use ($classes, $targetKey) {
            $q->whereNull('purchasable_type')
                ->orWhere(function ($q) use ($classes, $targetKey) {
                    $q->whereHasMorph('purchasable', $classes, function ($q) use ($targetKey) {
                        $q->whereKey($targetKey);
                    });
                });
        });
    }
}
