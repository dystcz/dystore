<?php

namespace Modules\DystoreAres\Domain\Ares\Entities;

use Generator;
use h4kuna\Ares\Ares\Core\Data;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class AresRecordStorage
{
    /**
     * @var Collection<AresRecord>
     */
    private Collection $records;

    private PhpRedisConnection $redis;

    public function __construct()
    {
        $this->redis = Redis::connection('cache');

        $redisPrefix = Config::get('database.redis.options.prefix');
        $cachePrefix = Config::get('cache.prefix');
        $prefix = "{$redisPrefix}{$cachePrefix}:";
        $pattern = '*ares:company:*';

        $keys = array_map(
            fn (string $key) => Str::after($key, $prefix),
            $this->redis->keys($pattern),
        );

        $this->records = Collection::make(Cache::getMultiple($keys))
            ->filter(fn ($data) => $data instanceof Data)
            ->mapWithKeys(fn (Data $data) => [
                $data->in => AresRecord::fromData($data),
            ]);
    }

    /**
     * Find ares record by company_in.
     */
    public function find(string $in): ?AresRecord
    {
        if (isset($this->records[$in])) {
            return $this->records[$in];
        }

        return null;
    }

    /**
     * @return Generator<AresRecord>
     */
    public function cursor(): Generator
    {
        foreach ($this->records as $record) {
            yield $record;
        }
    }

    /**
     * Get all cached records.
     *
     * @return AresRecord[]
     */
    public function all(): array
    {
        return iterator_to_array($this->cursor());
    }
}
