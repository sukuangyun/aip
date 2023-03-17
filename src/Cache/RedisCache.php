<?php

namespace Sukuangyun\Aip\Cache;

use Redis;

class RedisCache implements CacheInterface
{
    protected $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function get(string $key, $default = null)
    {
        $value = $this->redis->get($key);
        if ($value === null) {
            return $default;
        }
        return unserialize($value);
    }

    public function set(string $key, $data, int $ttl = 0): bool
    {
        $data = serialize($data);
        if (!$ttl) {
            return $this->redis->set($key, $data);
        }
        return $this->redis->set($key, $data, $ttl);
    }
}