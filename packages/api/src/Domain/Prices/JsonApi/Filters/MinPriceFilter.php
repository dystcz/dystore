<?php

namespace Dystore\Api\Domain\Prices\JsonApi\Filters;

use Illuminate\Support\Str;
use LaravelJsonApi\Eloquent\Contracts\Filter;
use LaravelJsonApi\Eloquent\Filters\Concerns\DeserializesValue;
use LaravelJsonApi\Eloquent\Filters\Concerns\HasColumn;
use LaravelJsonApi\Eloquent\Filters\Concerns\IsSingular;

/** @phpstan-consistent-constructor */
class MinPriceFilter implements Filter
{
    use DeserializesValue;
    use HasColumn;
    use IsSingular;

    private string $name;

    private string $column;

    /**
     * Create a new filter.
     *
     * @return static
     */
    public static function make(string $name, ?string $column = null): self
    {
        return new static($name, $column);
    }

    /**
     * CustomFilter constructor.
     */
    public function __construct(string $name, ?string $column = null)
    {
        $this->name = $name;
        $this->column = $column ?: Str::snake($name);
    }

    /**
     * {@inheritDoc}
     */
    public function key(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($query, $value)
    {
        return $query->where(
            $query->getModel()->qualifyColumn($this->column()),
            '>=',
            $this->deserialize($value)
        );
    }
}
