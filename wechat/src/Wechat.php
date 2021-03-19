<?php

namespace tianxin100\wechat;

use Curl\Curl;

/**
 * Created by IntelliJ IDEA.
 * User: jingjingjing
 * Date: 2018/11/8
 * Time: 9:55
 */
class Wechat
{
    public $errMsg = 0;
    public $errCode;

    public $appId;
    public $appSecret;
    public $mchId;
    public $apiKey;
    public $certPem;
    public $keyPem;


    /**
     * 微信支付组件
     *
     * @var Pay
     */
    public $pay;


    /**
     * Jsapi组件
     *
     * @var Jsapi
     */
    public $jsapi;


    /**
     * 模板消息组件
     *
     * @var TplMsg
     */
    public $tplMsg;

    /**
     * CURL
     *
     * @var Curl
     */
    public $curl;

    /**
     * 初始化
     *
     * @param array $args 初始化参数
     *
     * [
     *
     * 'appId' => '公众号appId',
     *
     * 'appSecret' => '公众号appSecret',
     *
     * 'mchId' => '微信支付商户id',
     *
     * 'apiKey' => '微信支付api密钥',
     *
     * 'certPem' => '微信支付cert证书路径（系统完整路径）',
     *
     * 'keyPem' => '微信支付key证书路径（系统完整路径）',
     *
     * 'cachePath' => '缓存路径（系统完整路径）',
     *
     * ]
     * @return Wechat|null
     */
    public function __construct($args = [])
    {
        $this->appId =isset($args['appId']) ? $args['appId'] : null; //"wxa1da0b715d809e7a";
        $this->appSecret =isset($args['appSecret']) ? $args['appSecret'] : null; //"cdf85c09649e8c1de26c4e214a26f4c8";
        $this->mchId =isset($args['mchId']) ? $args['mchId'] : null; // "1298152201";
        $this->apiKey = isset($args['apiKey']) ? $args['apiKey'] : null; //"cdf85c09649e8c1de26c4e214a26f4c8";
        $this->certPem = isset($args['certPem']) ? $args['certPem'] : null;
        $this->keyPem = isset($args['keyPem']) ? $args['keyPem'] : null;
	    $this->serialNo =isset($args['serialNo']) ? $args['serialNo'] : null; // "1298152201";
        return $this->init();
    }


    private function init()
    {
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);

        if ($this->certPem) {
            $this->curl->setOpt(CURLOPT_SSLCERTTYPE, 'PEM');
            $this->curl->setOpt(CURLOPT_SSLCERT, $this->certPem);
        }
        if ($this->keyPem) {
            $this->curl->setOpt(CURLOPT_SSLCERTTYPE, 'PEM');
            $this->curl->setOpt(CURLOPT_SSLKEY, $this->keyPem);
        }

        $this->pay = new Pay($this);
        $this->jsapi = new Jsapi($this);
        $this->tplMsg = new TplMsg($this);
        return $this;
    }

    /**
     * 获取微信接口的accessToken
     *
     * @param boolean $refresh 是否刷新accessToken
     * @param integer $expires accessToken缓存时间（秒）
     * @return string|null
     */
    public function getAccessToken($refresh = false, $expires = 3600)
    {
        return get_wechat_access_token($this->appId, $this->appSecret);
    }

    private function checkAccessToken($accessToken)
    {
        if (!$accessToken)
            return false;
        $api = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token={$accessToken}";
        $this->curl->get($api);
        $res = json_decode($this->curl->response, true);
        if (!empty($res['errcode']) && $res['errcode'] != 1)
            return false;
        return true;
    }


}