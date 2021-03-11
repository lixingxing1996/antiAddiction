<?php


namespace Lixingxing1996\AntiAddiction;

use GuzzleHttp\Client;
use AESGCM\AESGCM;
use Lixingxing1996\AntiAddiction\Exceptions\ErrorResponseException;
use Lixingxing1996\AntiAddiction\Exceptions\Exception;

class Authentication
{

    // 应用标识（APPID）
    protected $appId;
    // 游戏备案识别码（bizId）
    protected $bizId;
    // 用户密钥（Secret Key）
    protected $secretKey;

    protected $guzzleOptions = [];
    // 接口正式域名
    protected $host = 'https://api.wlc.nppa.gov.cn';
    // 请求地址
    protected $url;

    public function __construct(string $appId, string $bizId, string $secretKey, string $test_url = '')
    {
        $this->appId = $appId;
        $this->bizId = $bizId;
        $this->secretKey = $secretKey;
        $this->url = $test_url;

    }

    /**
     * name：接口加密
     * User:Lixingxing1996
     * DateTime：2021/3/10 10:52
     * @param   $param
     * @return string
     */
    protected function encryption($param)
    {
        $plaintext = json_encode($param);
        $cipher = strtolower('AES-128-GCM');

        // 二进制key
        $skey = hex2bin($this->secretKey);
        // 二进制iv
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        // 加密
        list($content, $tag) = AESGCM::encrypt($skey, $iv, $plaintext);
        $str = bin2hex($iv) . bin2hex($content) . bin2hex($tag);
        // 返回base64
        return base64_encode(hex2bin($str));
    }

    /**
     * name：接口签名
     * User:Lixingxing1996
     * DateTime：2021/3/10 10:52
     * @param array $headers
     * @param array $request
     * @return string
     */

    protected function sign(array $headers, array $request)
    {
        //1. 将除去 sign 的系统参数和除去请求体外的业务参数，根据参数的 key 进行字典排序，并按照 Key-Value 的格式拼接成一个字符串。将请求体中的参数拼接在字符串最后。
        $string = '';
        foreach ($headers as $key => $value) {
            if ($key == 'timestamps') {
                $param = parse_url($this->url);

                if (isset($param['query'])) {
                    parse_str($param['query'], $result);
                    foreach ($result as $k => $v) {
                        $string .= $k . $v;
                    }
                }
            }
            $string .= $key . $value;

        }
        $string .= json_encode($request);
        // 2.将 secretKey 拼接在步骤 1 获得字符串最前面，得到待加密字符串。
        $string = $this->secretKey . $string;

        // 3.使用 SHA256 算法对待加密字符串进行计算，得到数据签名。
        return hash("sha256", $string);

    }

    /**
     * name：获取毫秒级时间
     * User:Lixingxing1996
     * DateTime：2021/3/10 10:55
     * @return float
     */
    protected function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    /**
     * name：请求头信息
     * User:Lixingxing1996
     * DateTime：2021/3/10 11:00
     * @param array $request
     * @return array
     */
    protected function headers(array $request = [])
    {
        //   请求头信息
        $headers = [
            'appId' => $this->appId,
            'bizId' => $this->bizId,
            'timestamps' => $this->msectime()
        ];
        $headers['sign'] = $this->sign($headers, $request);
        return $headers;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * name：1.实名认证
     * User:Lixingxing1996
     * DateTime：2021/3/11 13:11
     * @param string $name 用户姓名
     * @param string $idNum 身份证号码
     * @param string $ai 游戏内部成员标识
     * @return mixed|string
     */
    public function check(string $name, string $idNum, string $ai)
    {
        $this->url = $this->url ? $this->url : $this->host . '/idcard/authentication/check';

        try {
            $request = [
                'data' => $this->encryption([
                    'ai' => $ai,
                    'name' => $name,
                    'idNum' => $idNum
                ])
            ];
            // 设置请求头
            $this->setGuzzleOptions([
                'headers' => $this->headers($request)
            ]);
            $response = $this->getHttpClient()->post($this->url, [
                'json' => $request,
            ])->getBody()->getContents();
            $result = json_decode($response, TRUE);

            // 如果返回状态代码 不是0 返回错误信息
            if ($result['errcode']) {
                throw new ErrorResponseException($result['errmsg'], $result['errcode']);
            }
            return $result['data']['result'];


        } catch (Exception $exception) {
            $message = $exception->getMessage();
            return $message;
        }


    }

    /**
     * name：2.实名认证结果查询
     * User:Lixingxing1996
     * DateTime：2021/3/11 13:14
     * @param string $ai 游戏内部成员标识
     * @return mixed|string
     */
    public function query(string $ai)
    {
        $this->url = $this->url ? $this->url . '?ai=' . $ai : $this->host . '/idcard/authentication/query?ai=' . $ai;

        try {
            // 设置请求头
            $this->setGuzzleOptions([
                'headers' => $this->headers()
            ]);

            $response = $this->getHttpClient()->get($this->url)->getBody()->getContents();
            $result = json_decode($response, TRUE);

            // 如果返回状态代码 不是0 返回错误信息
            if ($result['errcode']) {
                throw new ErrorResponseException($result['errmsg'], $result['errcode']);
            }
            return $result['data']['result'];


        } catch (Exception $exception) {
            $message = $exception->getMessage();
            return $message;
        }


    }

    /**
     * name：3.游戏用户行为数据上报
     * User:Lixingxing1996
     * DateTime：2021/3/11 13:57
     * @param object $collections
     * @param integer $collections [n].no 条目编码
     * @param string $collections [n].si 游戏内部会话标识
     * @param integer $collections [n].bt 用户行为类型 0下线 1上线
     * @param integer $collections [n].ot 行为发生时间 时间戳，秒
     * @param integer $collections [n].ct 上报类型 0 已认证通过用户 2 游客用户
     * @param integer $collections [n].di 设备标识
     * @param integer $collections [n].pi 用户唯一标识
     * @return mixed|string
     */
    public function behavior(object $collections)
    {
        $this->url = $this->url ? $this->url : $this->host . 'behavior/collection/loginout';
        try {
            $request = [
                'data' => $this->encryption($collections)
            ];
            // 设置请求头
            $this->setGuzzleOptions([
                'headers' => $this->headers($request)
            ]);
            $response = $this->getHttpClient()->post($this->url, [
                'json' => $request,
            ])->getBody()->getContents();
            $result = json_decode($response, TRUE);

            // 如果返回状态代码 不是0 返回错误信息
            if ($result['errcode']) {
                throw new ErrorResponseException($result['errmsg'], $result['errcode']);
            }
            return $result['data']['result'];


        } catch (Exception $exception) {
            $message = $exception->getMessage();
            return $message;
        }
    }
//用户唯一标识由38位的字符串构成，其中包括用户出生日期
//和用户编码两部分。用户出生日期以26进制（10个数字+英文字
//母表前16个字母）的方式编码，位于用户唯一标识前6位；用户
//编码由网络游戏防沉迷实名认证系统生成，位于用户唯一标识的
//后32位。

}