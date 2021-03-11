<h1 align="center">国家网络游戏防沉迷实名认证接口</h1>

<p align="center">网络游戏防沉迷实名认证系统 php SDK.</p>

## 安装

```shell
$ composer require lixingxing1996/antiAddiction -vvv
```

## 开发完善计划

1. 创建基础的防沉迷认证接口

## 使用帮助

1. 引入

```php
$ use Lixingxing1996\AntiAddiction\Authentication;
```

2. 实例化应用

```php
// $url 为测试url

$ new Authentication($appId, $bizId, $secretKey, $url);

```


PS: 当测试模式中，直接传递测试链接 例如测试上报接口：https://wlc.nppa.gov.cn/test/collection/loginout/9PrEdQ
直接传递

3. 条件有限，仅测试通过第一接口

## License

MIT