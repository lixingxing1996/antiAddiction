<?php

// 引入
require __DIR__ . '/vendor/autoload.php';
header("Content-Type:text/html;charset=utf-8");

use Lixingxing1996\AntiAddiction\Authentication;

//预置参数
//应用标识（APPID）： 游戏备案识别码（bizId）： 模式：
//用户密钥（Secret Key）： IP白名单： 暂无修改
$appId = 'f31a07e3bff344febb1107c52ee736fd';
$bizId = '1101999999';
$secretKey = '6dd17df292a3b8350378641e61496bbb';


$url = 'https://wlc.nppa.gov.cn/test/collection/loginout/9PrEdQ';

$test = new Authentication($appId, $bizId, $secretKey, $url);


$object = (object) [
   [
       'no'=>'111122',
       'si'=>'dsadswa',
       'bt'=>1,
       'ot'=>time(),
       'ct'=>2,
       'di'=>'12123',
       'pi'=>'1fffbjzos82bs9cnyj1dna7d6d29zg4esnh99u'
   ]
];



$re = $test->behavior($object);

var_dump($re);
exit;
