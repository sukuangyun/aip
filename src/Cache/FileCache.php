<?php

namespace Sukuangyun\Aip\Cache;

class FileCache implements CacheInterface
{

    protected $filePath;


    /**
     * 初始化
     *
     * @param string $filePath 缓存文件名
     */
    public function __construct(string $filePath = '')
    {
        if (!$filePath) {
            $filePath = __DIR__ . 'aip.cache';
        }
        $this->filePath = $filePath;
    }

    public function get(string $key, $default = null)
    {
        $content = $this->read();
        if (!$content) {
            return $default;
        }
        $data = unserialize($content);
        if (!isset($data[$key])) {
            return $default;
        }
        $item = $data[$key];
        if ($item['ttl'] < time()) {
            return $default;
        }
        return $item['value'];
    }

    protected function read()
    {
        $fh = @fopen($this->filePath, 'r');
        if ($fh === false) {
            return false;
        }
        $fsz = filesize($this->filePath);
        if (!$fsz) {
            return false;
        }
        $content = fread($fh, $fsz);
        fclose($fh);
        return $content;
    }

    public function set(string $key, $data, int $ttl = 0): bool
    {
        $content = $this->read();
        if ($content) {
            $arr = unserialize($content);
        }
        $arr[$key] = [
            'value' => $data,
            'ttl' => $ttl ? time() + $ttl : 2147483647
        ];
        $content = serialize($arr);
        $fh = fopen($this->filePath, 'w');
        fwrite($fh, $content);
        fclose($fh);
        return true;
    }
}