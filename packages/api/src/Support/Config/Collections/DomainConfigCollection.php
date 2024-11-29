<?php

namespace Dystore\Api\Support\Config\Collections;

use Dystore\Api\Support\Config\Data\DomainConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class DomainConfigCollection extends Collection
{
    /**
     * Create domain config collection.
     */
    public static function make($items = []): self
    {
        if (! empty($items)) {
            return new static($items);
        }

        return self::fromConfig('lunar-api.domains');
    }

    /**
     * Create domain config collection from a given config file.
     */
    public static function fromConfig(string $configKey): self
    {
        $items = array_map(function (array $domain) {
            return new DomainConfig(...$domain);
        }, Config::get($configKey, []));

        return new static($items);
    }

    /**
     * Get schemas from domain config.
     */
    public function getSchemas(): self
    {
        return $this->mapWithKeys(function (DomainConfig $domain) {
            if (! $domain->hasSchema()) {
                return [];
            }

            return [$domain->schema::type() => $domain->schema];
        });
    }

    /**
     * Get schemas from domain config.
     */
    public function getSchemaByType(string $type): string
    {
        return $this->firstWhere(
            fn (DomainConfig $domain) => $domain->schema::type() === $type,
        );
    }

    /**
     * Get routes from domain config.
     */
    public function getRoutes(): self
    {
        return $this->mapWithKeys(function (DomainConfig $domain) {
            if (! $domain->hasRoutes()) {
                return [];
            }

            return [$domain->schema::type() => $domain->routes];
        });
    }

    /**
     * Get models for Lunar model manifest.
     */
    public function getModelsForModelManifest(): self
    {
        return $this->mapWithKeys(function (DomainConfig $domain) {
            if (! $domain->swapsModel()) {
                return [];
            }

            return [$domain->model_contract => $domain->model];
        });
    }

    /**
     * Get policies from domain config.
     */
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
