<?php

namespace fsyd88\wxopen\api;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;

/**
 * Class BaseApi
 * @package app\extend\wxopen
 * @property string $component_appid  服务商appid
 * @property string $component_appsecret 服务商secret
 * @property string $component_verify_ticket ticket
 * @property string $authorizer_appid  授权appid
 * @property string $authorizer_refresh_token  授权刷新token
 */
class BaseApi extends BaseObject
{
    const BASE_API = 'https://api.weixin.qq.com/';

    public $component_appid;
    public $component_appsecret;
    public $component_verify_ticket;
    public $authorizer_appid;
    public $authorizer_refresh_token;

    /**
     * get token
     * @return object $result  json object
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/token/component_access_token.html
     */
    protected function getComponentToken()
    {
        $token = Yii::$app->cache->get('wxopen_component_access_token');
        if (!$token) {
            $res = $this->request('POST', 'cgi-bin/component/api_component_token', [
                'component_appid' => $this->component_appid,
                'component_appsecret' => $this->component_appsecret,
                'component_verify_ticket' => $this->component_verify_ticket
            ]);
            if ($res['errcode']) {
                throw new ResponseException($res, $res['errcode']);
            }
            Yii::$app->cache->set('wxopen_component_access_token', $res['component_access_token'], $res['expires_in']);
            $token = $res['component_access_token'];
        }
        return $token;
    }

    /**
     * 获取/刷新接口调用令牌
     * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/ThirdParty/token/api_authorizer_token.html
     */
    protected function getAuthorizerToken()
    {
        $authorization_code = Yii::$app->cache->get($this->authorizer_appid);
        if (!$authorization_code) {
            $res = $this->postByComponent('cgi-bin/component/api_authorizer_token', [
                'component_appid' => $this->component_appid,
                'authorizer_appid' => $this->authorizer_appid,
                'authorizer_refresh_token' => $this->authorizer_refresh_token
            ]);
            $authorization_code = $res['authorizer_access_token'];
            Yii::$app->cache->set($this->authorizer_appid, $authorization_code, $res['expires_in']);
        }
        return $authorization_code;
    }


    /**
     * 带 access_token POST 请求
     * @param string $uri api 地址
     * @param array $data post 数据
     * @return array $response
     */
    public function httpPost($uri, $data, $query = [])
    {
        if (!isset($query['access_token'])) {
            $query['access_token'] = $this->getAuthorizerToken();
        }
        $res = $this->request('POST', $uri, [
            'query' => $query,
            'body' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        return $this->checkResult($res, [$uri, $data, $query]);
    }

    /**
     * 带 access_token GET 请求
     * @param $uri
     * @param array $query
     * @return mixed|string
     * @throws ResponseException
     */
    public function httpGet($uri, $query = [])
    {
        if (!isset($data['access_token'])) {
            $data['access_token'] = $this->getAuthorizerToken();
        }
        $res = $this->request('GET', $uri, [
            'query' => $query,
        ]);
        return $this->checkResult($res, [$uri, $query]);
    }

    /**
     * 验证接口返回是否正确
     * @param string $result
     * @param array $errors 错误记录
     * @return boolean
     * @throws ResponseException
     */
    public function checkResult($result, $errors = [])
    {
        $res = Json::decode($result);
        if (isset($res['errcode']) && $res['errcode'] > 0) {
            Yii::error([$res, $errors]);
            throw new ResponseException($res, $res['errcode']);
        }
        return $res;
    }

    /**
     * 标准请求
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return mixed
     */
    public function request($method, $uri, $options)
    {
        $client = new Client(['base_uri' => self::BASE_API]);
        $response = $client->request($method, $uri, $options);
        $content = $response->getBody()->getContents();
        return $content;
    }
}
