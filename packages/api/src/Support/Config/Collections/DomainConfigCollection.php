<?php

namespace Dystore\Api\Support\Config\Collections;

use Dystore\Api\Support\Config\Data\DomainConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

final class DomainConfigCollection extends Collection
{
    public static function make($items = []): self
    {
        if (! empty($items)) {
            return new self($items);
        }

        return self::fromConfig('dystore.domains');
    }

    /**
     * Create domain config collection from a given config file.
     */
    public static function fromConfig(string $configKey): self
    {
        $items = array_map(function (array $domain) {
            return new DomainConfig(...$domain);
        }, Config::get($configKey, []));

        return new self($items);
    }

    public function getSchemas(): self
    {
        return $this
            ->map(fn (DomainConfig $domain) => $domain->schema)
            ->filter()
            ->values();
    }

    public function getResources(): self
    {
        return $this
            ->map(fn (DomainConfig $domain) => $domain->resource)
            ->filter()
            ->values();
    }

    public function getSchemaByType(string $type): string
    {
        return $this->firstWhere(
            fn (DomainConfig $domain) => $domain->schema::type() === $type,
        );
    }

    public function getRoutes(): self
    {
        return $this->mapWithKeys(function (DomainConfig $domain) {
            if (! $domain->hasRoutes()) {
                return [];
            }

            return [$domain->schema::type() => $domain->routes];
        });
    }

    public function getModelsForModelManifest(): self
    {
        return $this->mapWithKeys(function (DomainConfig $domain) {
            if (! $domain->swapsModel()) {
                return [];
            }

            return [$domain->model_contract => $domain->model];
        });
    }

    public function getPolicies(): self
    {
        return $this->mapWithKeys(function (DomainConfig $domain) {
            if (! $domain->hasPolicy()) {
                return [];
            }

            return [$domain->model => $domain->policy];
        });
    }
}
