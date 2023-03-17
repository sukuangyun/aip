<?php

namespace Sukuangyun\Aip;

class Config
{
    protected $appid;
    protected $apiKey;
    protected $secretKey;
    protected $reqTimeout;
    protected $baseUri;

    /**
     * 初始化配置
     *
     * @param string $appid
     * @param string $apiKey
     * @param string $secretKey
     * @param int $reqTimeout 请求超时时间
     * @param string $baseUri
     */
    public function __construct(string $appid, string $apiKey, string $secretKey, int $reqTimeout = 30, string $baseUri = '')
    {
        $this->appid = $appid;
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->reqTimeout = $reqTimeout;
        $this->baseUri = $baseUri ?: 'https://aip.baidubce.com';
    }

    public function appid(): string
    {
        return $this->appid;
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }

    public function secretKey(): string
    {
        return $this->secretKey;
    }

    public function reqTimeout(): int
    {
        return $this->reqTimeout;
    }

    public function baseUri(): string
    {
        return $this->baseUri;
    }
}