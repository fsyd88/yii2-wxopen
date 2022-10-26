<?php

namespace fsyd88\wxopen;

use fsyd88\wxopen\api\Component;
use fsyd88\wxopen\api\MsgCrypt;
use fsyd88\wxopen\api\NewTmpl;
use fsyd88\wxopen\api\Wxa;
use fsyd88\wxopen\api\WxOpen;
use fsyd88\wxopen\api\BaseApi;

/**
 * 微信 第三方平台
 * Class Factory
 * @package fsyd88\wxopen
 * @property Component $component component
 * @property NewTmpl $newtmpl newtmpl
 * @property WxOpen $wxopen wxopen
 * @property Wxa $wxa wxa
 * @property BaseApi $base base api
 */
class Factory
{

    private $_config;

    /**
     * wechat open
     * @param array $config [component_appid=>'',component_appsecret=>'',component_verify_ticket=>'',authorizer_appid=>'',authorizer_refresh_token=>'']
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
        if (!$config['component_appid'] || !$config['component_appsecret']) {
            throw new \Exception('component_appid and component_appsecret can not be null');
        }
    }

    /**
     * @return Component
     */
    public function getComponent()
    {
        return $this->createObject(Component::class);
    }

    /**
     * @return NewTmpl
     */
    public function getNewtmpl()
    {
        return $this->createObject(NewTmpl::class);
    }

    /**
     * @return WxOpen
     */
    public function getWxopen()
    {
        return $this->createObject(WxOpen::class);
    }

    /**
     * @return Wxa
     */
    public function getWxa()
    {
        return $this->createObject(Wxa::class);
    }

    /**
     * @return BaseApi
     */
    public function getBase()
    {
        return $this->createObject(BaseApi::class);
    }

    /**
     * @param $class
     * @return object the created object
     */
    private function createObject($class)
    {
        $params = $this->_config;
        $params['class'] = $class;
        return Yii::createObject($params);
    }
}
