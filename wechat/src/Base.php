<?php
/**
 * Created by IntelliJ IDEA.
 * User: jingjingjing
 * Date: 2018/11/8
 * Time: 11:32
 */

namespace tianxin100\wechat;


abstract class Base
{
    /**
     * 微信组件
     *
     * @var Wechat
     */
    protected $wechat;

    /**
     * @param Wechat $wechat
     */
    public function __construct($wechat)
    {
        $this->wechat = $wechat;
    }
}