<?php

// 引入
require __DIR__ .'/vendor/autoload.php';
header("Content-Type:text/html;charset=utf-8");
use Lixingxing1996\AntiAddiction\AntiAddiction;

//预置参数
//应用标识（APPID）： 游戏备案识别码（bizId）： 模式：
//用户密钥（Secret Key）： IP白名单： 暂无修改
$appId = 'f31a07e3bff344febb1107c52ee736fd';
$bizId = '1101999999';
$secretKey = '6dd17df292a3b8350378641e61496bbb';


$test = new AntiAddiction($appId,$bizId,$secretKey);


$re = $test->authCheck('某二一', '11000019010101000','200000000000000001');

var_dump($re);exit;
