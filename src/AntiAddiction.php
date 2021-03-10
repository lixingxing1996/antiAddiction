<?php


namespace Lixingxing1996\AntiAddiction;

use GuzzleHttp\Client;
use AESGCM\AESGCM;
use Lixingxing1996\AntiAddiction\Exceptions\ErrorResponseException;
use Lixingxing1996\AntiAddiction\Exceptions\Exception;

class AntiAddiction
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
    // 接口测试域名
    protected $test_host = 'https://wlc.nppa.gov.cn';

    public function __construct(string $appId, string $bizId, string $secretKey)
    {
        $this->appId = $appId;
        $this->bizId = $bizId;
        $this->secretKey = $secretKey;

    }

    /**
     * name：接口加密
     * User:Lixingxing1996
     * DateTime：2021/3/10 10:52
     * @param array $param
     * @return string
     */
    protected function encryption(array $param)
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
    protected function headers(array $request)
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

    //    1.实名认证
    public function authCheck(string $name, string $idNum, string $ai)
    {
//        $url = $this->host . '/idcard/authentication/check';
        $url = 'https://wlc.nppa.gov.cn/test/authentication/check/b87GKy';


        try {
            $request = [
                'data' => $this->encryption([
                    'ai' => $ai,
                    'name' => $name,
                    'idNum' => $idNum
                ])
            ];
            echo json_encode($request);
            echo "\n";
            // 设置请求头
            $this->setGuzzleOptions([
                'headers' => $this->headers($request)
            ]);
            echo json_encode($this->headers($request));

            $response = $this->getHttpClient()->post($url, [
                'json' => $request,
            ])->getBody()->getContents();
            $result = json_decode($response, TRUE);
            // 如果返回状态代码 不是0 返回错误信息
            if ($result['errcode']) {
                throw new ErrorResponseException('ddd');
            }
            return $result['data']['result'];


        } catch (Exception $exception) {
            $message = $exception->getMessage();
            return $message;
        }


    }
    // 2.实名认证结果查询
    // 3.游戏用户行为数据上报

    protected function errors($code){


    }

}