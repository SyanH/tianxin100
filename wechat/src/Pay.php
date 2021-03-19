<?php
/**
 * Created by IntelliJ IDEA.
 * User: jinjingjing
 * Date: 2018/11/8
 * Time: 10:18
 */

namespace tianxin100\wechat;

require_once  __DIR__ . '/lib/WxPay.Api.php';
require_once  __DIR__ . '/WxPay.JsApiPay.php';
require_once  __DIR__ . '/WxPay.Config.php';

class Pay extends Base
{
    public $WechatpaySerial='';

    /**
     * 统一下单
     * @param array $args [
     *
     * 'body' => '商品描述',
     *
     * 'detail' => '商品详情，选填',
     *
     * 'attach' => '附加数据，选填',
     *
     * 'out_trade_no' => '商户订单号，最大长度32',
     *
     * 'total_fee' => '订单总金额，单位为分',
     *
     * 'notify_url' => '异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数',
     *
     * 'trade_type' => '交易类型，可选值：JSAPI，NATIVE，APP',
     *
     * 'product_id' => '商品ID，trade_type=NATIVE时，此参数必传',
     *
     * 'openid' => '用户标识，trade_type=JSAPI时，此参数必传',
     *
     * ]
     *
     * @return array|boolean
     *
     */
    public function unifiedOrder($args)
    {
        $args['appid'] = $this->wechat->appId;
        $args['mch_id'] = $this->wechat->mchId;
        $args['nonce_str'] = md5(uniqid());
        $args['sign_type'] = 'MD5';
        $args['spbill_create_ip'] = '127.0.0.1';

        $args['sign'] = $this->makeSign($args);

        $xml = DataTransform::arrayToXml($args);

        $api = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $this->wechat->curl->post($api, $xml);

//        print_r( DataTransform::xmlToArray($this->wechat->curl->response));
//        exit;
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
//        $tools = new \JsApiPay();
//        $input = new \WxPayUnifiedOrder();
//        $input->SetBody($args['body']);
//        $input->SetAttach($args['body']);
//        $input->SetOut_trade_no($args['out_trade_no']);
//        $input->SetTotal_fee($args['total_fee']);
//        $input->SetTime_start(date("YmdHis"));
//        $input->SetTime_expire(date("YmdHis", time() + 600));
//        $input->SetGoods_tag("test");
//        $input->SetNotify_url($args['notify_url']);
//        $input->SetTrade_type($args['trade_type']);
//        $input->SetOpenid($args['openid']);
//        $config = new \WxPayConfig();
//
//        $order = \WxPayApi::unifiedOrder($config, $input);
//        $jsApiParameters = $tools->GetJsApiParameters($order);
//	    return $jsApiParameters;

    }


    public function orderQuery($order_no)
    {
        $data = [
            'appid' => $this->wechat->appId,
            'mch_id' => $this->wechat->mchId,
            'out_trade_no' => $order_no,
            'nonce_str' => md5(uniqid()),
        ];
        $data['sign'] = $this->makeSign($data);
        $xml = DataTransform::arrayToXml($data);
        $api = "https://api.mch.weixin.qq.com/pay/orderquery";
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
    }

    /**
     * 获取H5支付签名数据包
     * @param array $args [
     *
     * 'body' => '商品描述',
     *
     * 'detail' => '商品详情，选填',
     *
     * 'attach' => '附加数据，选填',
     *
     * 'out_trade_no' => '商户订单号，最大长度32',
     *
     * 'total_fee' => '订单总金额，单位为分',
     *
     * 'notify_url' => '异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数',
     *
     * 'openid' => '用户标识',
     *
     * ]
     *
     * @return array|null
     */
    public function getJsSignPackage($args)
    {
    }

    /**
     * 获取APP支付签名数据包
     * @param array $args [
     *
     * 'body' => '商品描述',
     *
     * 'detail' => '商品详情，选填',
     *
     * 'attach' => '附加数据，选填',
     *
     * 'out_trade_no' => '商户订单号，最大长度32',
     *
     * 'total_fee' => '订单总金额，单位为分',
     *
     * 'notify_url' => '异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数',
     *
     * ]
     *
     * @return array|null
     */
    public function getAppSignPackage($args)
    {
    }

    /**
     * 退款申请
     * @param array $args [
     *
     *
     * 'out_trade_no' => '商户订单号，最大长度32',
     *
     * 'out_refund_no' => '商户退款单号，最大长度32',
     *
     * 'total_fee' => '订单总金额，单位为分',
     *
     * 'refund_fee' => '退款总金额，单位为分',
     *
     * ]
     *
     * @return array|null
     */
    public function refund($args)
    {
        $args['appid'] = $this->wechat->appId;
        $args['mch_id'] = $this->wechat->mchId;
        $args['nonce_str'] = md5(uniqid());
        $args['op_user_id'] = $this->wechat->mchId;
        $args['sign'] = $this->makeSign($args);
        $xml = DataTransform::arrayToXml($args);
        $api = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
    }


    /**
     * 企业付款，企业向用户支付
     * @param array $args [
     *
     *
     * 'partner_trade_no' => '商户订单号，最大长度32',
     *
     * 'openid' => '用户openid',
     *
     * 'amount' => '提现金额，单位为分',
     *
     * 'desc' => '企业付款操作说明，例如：提现',
     *
     * ]
     */
    public function transfers($args)
    {
        $args['mch_appid'] = $this->wechat->appId;
        $args['mchid'] = $this->wechat->mchId;
        $args['nonce_str'] = md5(uniqid());
        $args['check_name'] = 'NO_CHECK';
        $args['spbill_create_ip'] = '127.0.0.1';
        $args['sign'] = $this->makeSign($args);
        $xml = DataTransform::arrayToXml($args);
        $api = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
        $this->wechat->curl->post($api, $xml);
        if (!$this->wechat->curl->response)
            return false;
        return DataTransform::xmlToArray($this->wechat->curl->response);
    }

    /**
     * 发放普通红包
     */
    public function sendRedPack($args)
    {
    }

    /**
     * 发放裂变红包
     */
    public function sendGroupRedPack($args)
    {
    }

    //签名
    public function makeSign($params){
        //签名步骤一：按字典序排序数组参数
        ksort($params);
        $string = $this->ToUrlParams($params);  //参数进行拼接key=value&k=v
        //签名步骤二：在string后加入KEY
        $string = $string . "&key={$this->wechat->apiKey}";
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($params)
    {
        $buff = "";
        foreach ($params as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

///////////////////////////////////////电商收付通代码//////////////////////////////////////////////
    //下载微信平台证书
    public function downloadCertificates()
    {
        $url = 'https://api.mch.weixin.qq.com/v3/certificates';
        $date = time();
        $nonce = date('YmdHis', time()) . rand(10000, 99999);
        $body="";
        $merchant_id=$this->wechat->mchId;
        $serial_no=$this->wechat->serialNo;//64CE982BC9A818E58A24EDEB166CAC26B0234405;
        $mch_private_key=$this->wechat->keyPem;      //商户私钥
        
        $sign = $this->sign($url,'GET',$date,$nonce,$body,$mch_private_key,$merchant_id,$serial_no);//$http_method要大写
        // 含有服务器用于验证商户身份的凭证
        $header = [
            'Accept:application/json',
            'Authorization:WECHATPAY2-SHA256-RSA2048 ' . $sign,
            'User-Agent:' . $merchant_id,
        ];
        $result = $this->curl_get($url, $header);
        $result = json_decode($result,true);
        $serial_no=$result['data'][0]['serial_no'];
        file_put_contents('./serial_no.txt', $serial_no);
        $encrypt_certificate=$result['data'][0]['encrypt_certificate'];

        $res=$this->decryptToString($encrypt_certificate['associated_data'], $encrypt_certificate['nonce'], $encrypt_certificate['ciphertext']);
        file_put_contents('./public.pem', $res);
        return $res;
    }
    // 平台证书解密
    public function decryptToString($associatedData, $nonceStr, $ciphertext)
    {
        $aesKey='c67c119f59e163c57182066539b70730';//服务商apiv3密钥
        $ciphertext = \base64_decode($ciphertext);
        if (strlen($ciphertext) <= 32) {
            return false;
        }
        if (PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods())) {
            $ctext = substr($ciphertext, 0, -16);
            $authTag = substr($ciphertext, -16);
            return \openssl_decrypt($ctext, 'aes-256-gcm', $aesKey, \OPENSSL_RAW_DATA, $nonceStr,$authTag, $associatedData);
        }

        throw new \RuntimeException('AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php');
    }
    //加密
    private function getEncrypt($str){ 
        //$str是待加密字符串 
        $public_key_path = './public.pem';
        $public_key = file_get_contents($public_key_path); 
        $encrypted = ''; 
        if (openssl_public_encrypt($str,$encrypted,$public_key,OPENSSL_PKCS1_OAEP_PADDING)) { 
            //base64编码 
            $sign = base64_encode($encrypted);
        } else {
            throw new Exception('encrypt failed');
        }
        return $sign;
    } 

     
    //二级商户进件API
    public function applyments($mch){
        // 校验参数
        $mch['business_license_info'] = json_decode($mch['business_license_info'],ture);
        $mch['id_doc_info'] = json_decode($mch['id_doc_info'],ture);
        $mch['account_info'] = json_decode($mch['account_info'],ture);
        $mch['contact_info'] = json_decode($mch['contact_info'],ture);
        $mch['sales_scene_info'] = json_decode($mch['sales_scene_info'],ture);
        $data = [
            'out_request_no'    => $mch['out_request_no'],    // 业务申请编号
            'organization_type' => $mch['organization_type'],//'2401'
            'merchant_shortname' => $mch['name'],//商户简称
        ];
        //营业执照/登记证书信息
        //1、主体为“小微/个人卖家”时，不填。2、主体为“个体工商户/企业”时，请上传营业执照。3、主体为“党政、机关及事业单位/其他组织”时，请上传登记证书。
        if($mch['organization_type'] != 2401){
            $data['business_license_info']['business_license_copy']=json_decode($this->run($mch['business_license_info']['business_license_copy']))->media_id;//证件扫描件
            $data['business_license_info']['business_license_number']=$mch['business_license_info']['business_license_number'];//证件注册号
            $data['business_license_info']['merchant_name']=$mch['business_license_info']['merchant_name'];//商户名称
            $data['business_license_info']['legal_person']=$mch['business_license_info']['legal_person'];//经营者/法定代表人姓名
            $data['business_license_info']['company_address']=$mch['business_license_info']['company_address'];//注册地址
            $data['business_license_info']['business_time']=$mch['business_license_info']['business_time'];//营业期限
        }
        //组织机构代码证信息
        //主体为“企业/党政、机关及事业单位/其他组织”，且营业执照/登记证书号码不是18位时必填。
        if(($mch['organization_type'] == 3 || (strlen($data['organization_cert_info']['organization_number']) < 18)) && $mch['organization_type'] != 2401){
            $data['organization_cert_info']['organization_copy']=json_decode($this->run($mch['organization_cert_info']['organization_copy']))->media_id;//证件扫描件
            $data['organization_cert_info']['organization_number']=$mch['organization_cert_info']['organization_number'];//证件注册号
            $data['organization_cert_info']['organization_time']=$mch['organization_cert_info']['organization_time'];//商户名称
        }
        //-经营者/法人身份证信息
        if(!$mch['id_doc_type'] || $mch['id_doc_type'] == 'IDENTIFICATION_TYPE_MAINLAND_IDCARD'){
            $card_front_media=json_decode($this->run($mch['card_front']));
            $card_obverse_media=json_decode($this->run($mch['card_obverse']));
            $name=$this->getEncrypt($mch['id_card_name']);
            $data['id_card_info'] = [
                'id_card_copy'      => $card_front_media->media_id,    // 身份证人像面照片  media_id
                'id_card_national'  => $card_obverse_media->media_id,    // 身份证国徽面照片
                'id_card_name'      => $name,
                'id_card_number'    => $this->getEncrypt($mch['id_card_num']),
                'id_card_valid_time'=> $mch['id_card_valid_time'],    // '["1970-01-01","长期"]' string(50)
            ];
        }
        //经营者/法人其他类型证件信息
        if($mch['id_doc_type'] && $mch['id_doc_type'] != 'IDENTIFICATION_TYPE_MAINLAND_IDCARD'){
            $id_doc_info=json_decode($this->run($mch['id_doc_info']['id_doc_copy']));
            $data['id_doc_info'] = [
                'id_doc_name'      => $this->getEncrypt($mch['id_doc_info']['id_doc_name']),    // 请填写经营者/法人姓名。
                'id_doc_number'    => $this->getEncrypt($mch['id_doc_info']['id_doc_number']),    // 7~11位 数字|字母|连字符 。
                'id_doc_copy'      => $id_doc_info->media_id,//证件照片
                'doc_period_end'=> $mch['id_doc_info']['doc_period_end'],    //证件结束日期 '["1970-01-01","长期"]' string(50)
            ];
        }
        //结算银行账户
        $data['need_account_info'] = true;
        $data['account_info'] = [
            'bank_account_type' => $mch['account_info']['bank_account_type'], //74-对公账户、75-对私账户
            'account_bank'      => $mch['account_info']['account_bank'],
            'account_name'      => $this->getEncrypt($mch['account_info']['account_name']),
            'bank_address_code' => $mch['account_info']['bank_address_code'],
            'account_number'    => $this->getEncrypt($mch['account_info']['account_number']),
        ];
        //超管信息
        $data['contact_info'] = [
            'contact_type' => $mch['contact_info']['contact_type'],
            'contact_name' => $this->getEncrypt($mch['contact_info']['contact_name']),
            'contact_id_card_number' => $this->getEncrypt($mch['contact_info']['contact_id_card_number']),
            'mobile_phone' =>$this->getEncrypt($mch['contact_info']['mobile_phone']), //$this->getEncrypt('18438628377'),
            'contact_email' =>$this->getEncrypt($mch['contact_info']['contact_email']), 
        ];
        //店铺信息
        $data['sales_scene_info'] = [
            'store_name'   => $mch['name'],
            'store_url'    => $mch['store_url']//'http://pay.pinwen.org/web/mch.php?store_id=1',
        ];
        //特殊资质
        if($mch->qualifications){
            $data['qualifications']=$mch['qualifications'];//特殊资质
        }
        //补充材料
        if($mch->business_addition_pics){
            $data['business_addition_pics']=$mch['business_addition_pics'];//补充材料
        }
        //补充说明
        if($mch->business_addition_desc){
            $data['business_addition_desc']=$mch['business_addition_desc'];//补充说明
        }
        $merchant_id=$this->wechat->mchId;
        $serial_no=$this->wechat->serialNo;
        $mch_private_key=$this->wechat->keyPem;      //商户私钥
        $json_data =json_encode($data);
        $url      = 'https://api.mch.weixin.qq.com/v3/ecommerce/applyments/';
        $boundary = uniqid(); //分割符号
        $date = time();
        $nonce = date('YmdHis', time()) . rand(10000, 99999);
        $sign = $this->sign($url,'POST',$date,$nonce,$json_data,$mch_private_key,$merchant_id,$serial_no);//$http_method要大写
        $WechatpaySerial=file_get_contents('./serial_no.txt');
        $header= [
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.108 Safari/537.36',
            'Accept:application/json',
            'Authorization:WECHATPAY2-SHA256-RSA2048 '.$sign,
            'Content-Type:application/json;boundary='.$boundary,
            'Wechatpay-Serial:'.$WechatpaySerial,
        ];
        
        $r = $this->doCurl($url,$json_data,$header);
        $res=json_decode($r,true);
        return $res;
    }
    //查询商户申请 状态
    public function queryStatus($out_request_no)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/applyments/out-request-no/'.$out_request_no;
        $args='';
        $result=$this->alluse($url,'GET',$args);
        return $result;
    }

    //合单支付
    public function combinePay($args){
        // 校验参数
        $url      = 'https://api.mch.weixin.qq.com/v3/combine-transactions/jsapi';
        $json_data =json_encode($args);
        $result=$this->alluse($url,'POST',$json_data);
        return $result;
    }

    //合单支付查询状态
    public function combineTransactions($out_request_no)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/combine-transactions/out-trade-no/'.$out_request_no;
        $args='';
        $result=$this->alluse($url,'GET',$args);
        return $result;
    }

    //关闭订单
    public function combineTransactionsClose($args,$combine_out_trade_no){
        // 校验参数
        $json_data =json_encode($args);
        $url = 'https://api.mch.weixin.qq.com/v3/combine-transactions/out-trade-no/'.$combine_out_trade_no.'/close';
        $json_data =json_encode($args);
        $result=$this->alluse($url,'POST',$json_data);
        return $result;
        
    }

    //合单支付------退款
    public function refundsApply($args){
        // 校验参数
        $args['sp_appid'] = $this->wechat->appId;
        $url      = 'https://api.mch.weixin.qq.com/v3/ecommerce/refunds/apply';
        $json_data =json_encode($args);
        $result=$this->alluse($url,'POST',$json_data);
        return $result;
    }

    //合单支付------添加分账接受方
    public function profitSharingAddreceiver($args){
        // 校验参数
        $args['appid'] = $this->wechat->appId;
        $url      = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/receivers/add';
        $json_data =json_encode($args);
        $result=$this->alluse($url,'POST',$json_data);
        return $result;
    }

    //合单支付------分账
    public function profitSharingOrder($args){
        // 校验参数
        $args['appid'] = $this->wechat->appId;
        $url      = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/orders';
        $json_data =json_encode($args);
        $result=$this->alluse($url,'POST',$json_data);
        return $result;
    }

    //查询二级商户账户提现状态
    public function getProfitSharingOrder($sub_mchid,$out_order_no,$transaction_id){
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/orders?sub_mchid='.$sub_mchid.'&transaction_id='.$transaction_id.'&out_order_no='.$out_order_no;
        $args='';
        $result=$this->alluse($url,'GET',$args);
        return $result;
    }

    //合单支付------完结分账
    public function finishProfitSharingOrder($args){
        // 校验参数
        $args['appid'] = $this->wechat->appId;
        $url      = 'https://api.mch.weixin.qq.com/v3/ecommerce/profitsharing/finish-order';
        $json_data =json_encode($args);
        $result=$this->alluse($url,'POST',$json_data);
        return $result;
    }

    //查询二级商户账户余额
    public function balance($sub_mchid){
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/fund/balance/'.$sub_mchid;
        $args='';
        $result=$this->alluse($url,'GET',$args);
        return $result;
    }

    //合单支付------余额提现
    public function withdraw($args){
        // 校验参数
        $url      = 'https://api.mch.weixin.qq.com/v3/ecommerce/fund/withdraw';
        $json_data =json_encode($args);
        $result=$this->alluse($url,'POST',$json_data);
        return $result;
    }

    //查询二级商户账户提现状态
    public function withdraw_query($sub_mchid,$out_request_no){
        $url = 'https://api.mch.weixin.qq.com/v3/ecommerce/fund/withdraw/out-request-no/'.$out_request_no.'?sub_mchid='.$sub_mchid;
        $args='';
        $result=$this->alluse($url,'GET',$args);
        return $result;
    }

    public function alluse($url,$method,$args){
        $merchant_id=$this->wechat->mchId;
        $serial_no=$this->wechat->serialNo;
        $mch_private_key=$this->wechat->keyPem;      //商户私钥
        $boundary = uniqid(); //分割符号
        $date = time();
        $nonce = date('YmdHis', time()) . rand(10000, 99999);
        $sign = $this->sign($url,$method,$date,$nonce,$args,$mch_private_key,$merchant_id,$serial_no);//$http_method要大写
        $header= [
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.108 Safari/537.36',
            'Accept:application/json',
            'Authorization:WECHATPAY2-SHA256-RSA2048 '.$sign,
            'Content-Type:application/json;boundary='.$boundary
        ];
        if($method == "POST"){
            $result = $this->doCurl($url,$args,$header);
        }else{
            $result = $this->curl_get($url, $header);
        }
        $result = json_decode($result,true);
        return $result;
    }

    //签名
    public function createSign($params)
    {
        //签名步骤一：按字典序排序参数
           ksort($params);
           $String = $this->formatBizQueryParaMap($params, false);

           //签名步骤二：在string后加入KEY
           $String = $String."&key=".$this->wechat->mch_apiKey;

           //签名步骤三：加密方式HMAC-SHA256
           // $String = md5($String); 
           $String = hash_hmac("sha256", $String,$this->wechat->mch_apiKey);
           //签名步骤四：所有字符转为大写
           $result_ = strtoupper($String);

           return $result_;
    }
      //按字典序排序参数
    private function formatBizQueryParaMap($params,$urlencode=false)
    {
        ksort($params);
        $buff = '';
        foreach($params as $key=>$val)
        {
            $buff .= $key . "=" . $val . "&";
        }
        $reqPar = substr($buff, 0, strlen($buff)-1);
        return $reqPar;
    }


    ////////////////////////////微信支付图片上传////////////////////////
    public function run($filename)
    {
        $file=explode('/', $filename);
        $url = 'https://api.mch.weixin.qq.com/v3/merchant/media/upload';
        $merchant_id=$this->wechat->mchId;
        $serial_no=$this->wechat->serialNo;
        $mch_private_key=$this->wechat->keyPem;      //商户私钥
        $mime_type = 'image/jpeg';
        $data['filename'] = $file[7];
        $meta['filename'] = $file[7];
        $meta['sha256'] = hash_file('sha256',$filename);
        $boundary = uniqid(); //分割符号
        $date = time();
        $nonce = date('YmdHis', time()) . rand(10000, 99999);
        $sign = $this->sign($url,'POST',$date,$nonce,json_encode($meta),$mch_private_key,$merchant_id,$serial_no);//$http_method要大写
        $header[] = 'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.108 Safari/537.36';
        $header[] = 'Accept:application/json';
        $header[] = 'Authorization:WECHATPAY2-SHA256-RSA2048 '.$sign;
        $header[] = 'Content-Type:multipart/form-data;boundary='.$boundary;

        $boundaryStr = "--{$boundary}\r\n";
        $out = $boundaryStr;
        $out .= 'Content-Disposition: form-data; name="meta"'."\r\n";
        $out .= 'Content-Type: multipart/form-data'."\r\n";
        $out .= "\r\n";
        $out .= json_encode($meta)."\r\n";
        $out .=  $boundaryStr;
        $out .= 'Content-Disposition: form-data; name="file"; filename="'.$data['filename'].'"'."\r\n";
        $out .= 'Content-Type: '.$mime_type.';'."\r\n";
        $out .= "\r\n";
        $out .= file_get_contents($filename)."\r\n";
        $out .= "--{$boundary}--\r\n";
        $r = $this->doCurl($url,$out,$header);
        return $r;
    }

    private function nonce_str()
    {
        return date('YmdHis', time()) . rand(10000, 99999);
    }

    public function curl_get($url,$header){
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 超时设置,以秒为单位
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
     
        // 超时设置，以毫秒为单位
        // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);
     
        // 设置请求头
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        //执行命令
        $data = curl_exec($curl);
     
        // 显示错误信息
        if (curl_error($curl)) {
            print "Error: " . curl_error($curl);
        } else {
            // 打印返回的内容
            return $data;
            curl_close($curl);
        }
    }
    public function doCurl($url, $data , $header = array(), $referer = '', $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        // 模拟来源
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        $response = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        curl_close($ch);
        return $response;
    }

    //获取私钥
    public static function getPrivateKey($filepath) {
        return openssl_get_privatekey(file_get_contents($filepath));
    }

    //签名
    private function sign($url,$http_method,$timestamp,$nonce,$body,$mch_private_key,$merchant_id,$serial_no)
    {

        $url_parts = parse_url($url);
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        $message =
            $http_method."\n".
            $canonical_url."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";
        if (!in_array('sha256WithRSAEncryption', \openssl_get_md_methods(true))) {
            var_dump("当前PHP环境不支持SHA256withRSA");
        }
        openssl_sign($message, $raw_sign, openssl_get_privatekey(file_get_contents($mch_private_key)), 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);
        $schema = 'WECHATPAY2-SHA256-RSA2048 ';
        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $merchant_id, $nonce, $timestamp, $serial_no, $sign);
        return $token;
    }

    //签名
    public function mSign($data)
    {
        $mch_private_key=$this->wechat->keyPem;      //商户私钥
        $appid=$data["appId"];
        $nonce = $data['nonceStr'];
        $date = $data['timeStamp'];
        $package = $data['package'];

        $url_parts = parse_url($url);
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        $message = $appid."\n".
            $date."\n".
            $nonce."\n".
            $package."\n";
        //var_dump($message);
        if (!in_array('sha256WithRSAEncryption', \openssl_get_md_methods(true))) {
            var_dump("当前PHP环境不支持SHA256withRSA");
        }

        openssl_sign($message, $raw_sign, openssl_get_privatekey(file_get_contents($mch_private_key)), 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);
        //var_dump($sign);
        return $sign;
    }
    
}