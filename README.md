## 百度识图服务封装

### 安装
```
composer require sukuangyun/aip
```

### 使用

默认实现了文件缓存管理器来缓存accessToken，正常使用时请替换该缓存管理器，例如使用Redis等。 只需实现get()和set()方法即可。

```php
$rds = new \Redis();
$rds->connect('127.0.0.1', 6379);
$rds->auth('password');
$cache = new \Sukuangyun\Aip\Cache\RedisCache($rds);
$aip = new \Sukuangyun\Aip\Aip(
    new \Sukuangyun\Aip\Config('appid', 'apikey', 'secretKey'),
    $cache
 );

// 在thinkphp中可以使用
// $aip = new Aip(new Config('appid', 'apikey', 'secretKey'), cache()->cache);
```

已封装的识图服务使用

```php
<?php

use Sukuangyun\Aip\Aip;
use Sukuangyun\Aip\Config;

require_once 'vendor/autoload.php';

$imagePath = __DIR__ . DIRECTORY_SEPARATOR . 'test_id_card.jpg';
// $rs = file_get_contents($imagePath);
// $image = base64_encode($rs);
$aip = new Aip(new Config('appid', 'apikey', 'secretKey'));
$image = $aip->getImageBase64ByPath($imagePath);
$resp = $aip->idCard($image, 'back');
var_dump($resp);
```

自定义识图服务请求

```php
require_once 'vendor/autoload.php';

$imagePath = '火车票图片路径';
// $rs = file_get_contents($imagePath);
// $image = base64_encode($rs);
$aip = new Aip(new Config('appid', 'apikey', 'secretKey'));
$image = $aip->getImageBase64ByPath($imagePath);
$resp = $aip->post('/rest/2.0/ocr/v1/train_ticket', [
    'form_params' => [
        'image' => $image
    ]
]);
var_dump($resp);
```
