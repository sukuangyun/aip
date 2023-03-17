<?php
namespace Sukuangyun\Aip\Cache;

interface CacheInterface
{
    /**
     * 获取缓存数据
     *
     * @param string $key
     * @param $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * 设置缓存数据
     *
     * @param string $key   缓存标识
     * @param mixed $data   缓存数据
     * @param int|null $ttl 缓存时间
     * @return bool
     */
    public function set(string $key, $data, ?int $ttl = null): bool;
}