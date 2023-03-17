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

$resp = $aip->post('/rest/2.0/ocr/v1/train_ticket', [
    'form_params' => [
        'image' => '火车票图片base64内容'
    ]
]);
var_dump($resp);