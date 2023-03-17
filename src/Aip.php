<?php

namespace Sukuangyun\Aip;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Sukuangyun\Aip\Cache\CacheInterface;
use Sukuangyun\Aip\Cache\FileCache;
use Sukuangyun\Aip\Exceptions\AipException;

class Aip
{
    /**
     * 配置
     *
     * @var Config
     */
    protected $config;

    /**
     * 缓存
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected static $cachePrefix = 'aip:cache:';

    /**
     * 初始化识图服务
     *
     * @param Config $config             配置
     * @param CacheInterface|null $cache 缓存
     */
    public function __construct(Config $config, ?CacheInterface $cache = null)
    {
        $this->config = $config;
        $this->cache = $cache ?: new FileCache;
    }


    /**
     * 获取配置
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * 获取accessToken
     *
     * @return string
     * @throws GuzzleException
     */
    public function getAccessToken(): string
    {
        $accessToken = $this->cache->get(static::$cachePrefix . 'access_token');
        if ($accessToken) {
            return $accessToken;
        }
        $resp = $this->cli()->post('/oauth/2.0/token', [
            'query' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->config->apiKey(),
                'client_secret' => $this->config->secretKey()
            ],
            'json' => []
        ]);
        $content = $resp->getBody()->getContents();
        $result = json_decode($content, true);
        if (isset($result['access_token'])) {
            $expiresIn = $result['expires_in'] ?? 60 * 60 * 24 * 10;
            $this->cache->set(static::$cachePrefix . 'access_token', $result['access_token'], $expiresIn);
        }
        return $result['access_token'];
    }

    /**
     * 客户端
     *
     * @return Client
     */
    protected function cli(): Client
    {
        return new Client([
            'timeout' => $this->config->reqTimeout(),
            'base_uri' => $this->config->baseUri()
        ]);
    }

    /**
     * 身份证识别
     *
     * @param string $image
     * @param string $side   人像面和国徽面
     * @param array $options 其他参数
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function idCard(string $image, string $side = '', array $options = []): array
    {
        $formParams = ['image' => $image];
        if ($side) {
            $formParams['id_card_side'] = $side;
        }
        if ($options) {
            $formParams = array_merge($formParams, $options);
        }
        return $this->post('/rest/2.0/ocr/v1/idcard', [
            'form_params' => $formParams
        ]);
    }

    /**
     * 驾驶证识别
     *
     * @param string $image
     * @param string $side
     * @param array $options
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function drivingLicense(string $image, string $side = '', array $options = []): array
    {
        $formParams = ['image' => $image];
        if ($side) {
            $formParams['driving_license_side'] = $side;
        }
        if ($options) {
            $formParams = array_merge($formParams, $options);
        }
        return $this->post('/rest/2.0/ocr/v1/driving_license', [
            'form_params' => $formParams
        ]);
    }

    /**
     * 道路运输证
     *
     * @param string $image
     * @param array $options
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function roadTransportCertificate(string $image, array $options = []): array
    {
        $formParams = ['image' => $image];
        if ($options) {
            $formParams = array_merge($formParams, $options);
        }
        return $this->post('/rest/2.0/ocr/v1/road_transport_certificate', [
            'form_params' => $formParams
        ]);
    }

    /**
     * 行驶证
     *
     * @param string $image
     * @param string $side
     * @param array $options
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function vehicleLicense(string $image, string $side = '', array $options = []): array
    {
        $formParams = ['image' => $image];
        if ($side) {
            $formParams['vehicle_license_side'] = $side;
        }
        if ($options) {
            $formParams = array_merge($formParams, $options);
        }
        return $this->post('/rest/2.0/ocr/v1/vehicle_license', [
            'form_params' => $formParams
        ]);
    }

    /**
     * 车牌识别
     *
     * @param string $image
     * @param array $options
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function vehiclePlate(string $image, array $options = []): array
    {
        $formParams = ['image' => $image];
        if ($options) {
            $formParams = array_merge($formParams, $options);
        }
        return $this->post('/rest/2.0/ocr/v1/license_plate', [
            'form_params' => $formParams
        ]);
    }

    /**
     * 营业执照识别
     *
     * @param string $image
     * @param array $options
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function businessLicense(string $image, array $options = []): array
    {
        $formParams = ['image' => $image];
        if ($options) {
            $formParams = array_merge($formParams, $options);
        }
        return $this->post('/rest/2.0/ocr/v1/business_license', [
            'form_params' => $formParams
        ]);
    }

    /**
     * 银行卡识别
     *
     * @param string $image
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function bankcard(string $image): array
    {
        return $this->post('/rest/2.0/ocr/v1/bankcard', [
            'form_params' => [
                'image' => $image
            ]
        ]);
    }

    /**
     * post请求
     *
     * @throws AipException
     * @throws GuzzleException
     */
    public function post(string $uri, array $options = []): array
    {
        $token = ['access_token' => $this->getAccessToken()];
        $options['query'] = isset($options['query']) ? array_merge($options['query'], $token) : $token;
        $content = $this->cli()->post($uri, $options)->getBody()->getContents();
        $result = json_decode($content, true);
        if (!is_array($result)) {
            throw new AipException('请求出错：' . PHP_EOL . $content);
        }
        return $result;
    }

    /**
     * 护照识别
     *
     * @param string $image
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function passport(string $image): array
    {
        return $this->post('/rest/2.0/ocr/v1/passport', [
            'form_params' => [
                'image' => $image
            ]
        ]);
    }

    /**
     * 社保卡识别
     *
     * @param string $image
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function socialSecurityCard(string $image): array
    {
        return $this->post('/rest/2.0/ocr/v1/social_security_card', [
            'form_params' => [
                'image' => $image
            ]
        ]);
    }

    /**
     * 户口本识别
     *
     * @param string $image
     * @param bool $isHomePage
     * @return array
     * @throws AipException
     * @throws GuzzleException
     */
    public function householdRegister(string $image, bool $isHomePage = false): array
    {
        return $this->post('/rest/2.0/ocr/v1/household_register', [
            'form_params' => [
                'image' => $image,
                'household_register_side' => $isHomePage ? 'homepage' : 'subpage'
            ]
        ]);
    }

    /**
     * 获取base64图片
     *
     * @param string $imagePath 本地或网络路径
     * @return string
     */
    public function getImageBase64ByPath(string $imagePath): string
    {
        return base64_encode(file_get_contents($imagePath));
    }
}