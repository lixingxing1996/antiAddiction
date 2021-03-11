<?php

namespace Lixingxing1996\AntiAddiction\Exceptions;


class ErrorResponseException extends Exception
{

    public function __construct($message = "")
    {

        $this->message = $this->lang($message);
       
    }


    public function lang($msg)
    {
        $langs = [
            'SYS ERROR' => '系统错误',
            'SYS REQ RESOURCE NOT EXIST' => '接口请求的资源不存在',
            'SYS REQ METHOD ERROR' => '接口请求方式错误',
            'SYS REQ HEADER MISS ERROR' => '接口请求核心参数缺失',
            'SYS REQ IP ERROR' => '接口请求IP地址非法',
            'SYS REQ BUSY ERROR' => '接口请求超出流量限制',
            'SYS REQ EXPIRE ERROR' => '接口请求过期',
            'SYS REQ PARTNER ERROR' => '接口请求方身份非法',
            'SYS REQ PARTNER AUTH DISABLE' => '接口请求方权限未启用',
            'SYS REQ AUTH ERROR' => '接口请求方无该接口权限',
            'SYS REQ PARTNER AUTH ERROR' => '接口请求方身份核验错误',
            'SYS REQ PARAM CHECK ERROR' => '接口请求报文核验失败',
            'TEST SYS ERROR' => '测试系统错误',
            'TEST TASK NOT EXIST' => '测试任务不存在',
            'TEST PARAM INVALID ERROR' => '测试参数无效',
            'BUS AUTH IDNUM ILLEGAL' => '身份证号格式校验失败',
            'BUS AUTH RESOURCE LIMIT' => '实名认证条目已达上限',
            'BUS AUTH CODE NO AUTH RECODE' => '无该编码提交的实名认证记录',
            'BUS AUTH CODE ALREADY IN USE' => '编码已经被占用',
            'BUS COLL PARTIAL ERROR' => '行为数据部分上报失败',
            'BUS COLL BEHAVIOR NULL ERROR' => '行为数据为空',
            'BUS COLL OVER LIMIT COUNT' => '行为数据超出条目数量限制',
            'BUS COLL NO INVALID' => '行为数据编码错误',
            'BUS COLL BEHAVIOR TIME ERROR' => '行为发生时间错误',
            'BUS COLL PLAYER MODE INVALID' => '用户类型无效',
            'BUS COLL BEHAVIOR MODE INVALID' => '行为类型无效',
            'BUS COLL PLAYERID MISS' => '缺失PI（用户唯一标识）值',
            'BUS COLL DEVICEID MISS' => '缺失DI（设备标识）值',
            'BUS COLL PLAYERID INVALID' => 'PI（用户唯一标识）值无效',
        ];

        return isset($langs[$msg]) ? $langs[$msg] : $msg;
    }


}


