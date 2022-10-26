<?php

namespace fsyd88\wxopen\api;

/**
 * 新的模板功能，订阅消息接口
 * 订阅消息设置接口详情请参考下方接口列表，但调用接口前请注意以下两点
 * 1、当服务商调用时请使用第三方平台接口调用令牌authorizer_access_token以调用订阅消息设置接口。
 * 2、请务必先完成授权后再调用，否则会出现61007的错误
 * @author ZHAO
 * https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Business/SubcribeMessage.html
 */
class NewTmpl extends BaseApi
{

    /**
     * 获取当前帐号所设置的类目信息
     * 本接口用于获取小程序帐号当前所设置的类目
     * @return type
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getCategory.html
     */
    public function getCategory()
    {
        return $this->httpGet('wxaapi/newtmpl/getcategory');
    }

    /**
     * 获取模板标题列表
     * @param string $ids 类目 id，多个用逗号隔开
     * @param string $keyword 搜索关键词
     * @param int $start 用于分页，表示从 start 开始。从 0 开始计数
     * @param int $limit 用于分页，表示拉取 limit 条记录。最大为 30
     * @return mixed|string
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getPubTemplateTitleList.html
     */
    public function getPubTemplateTitles($ids, $keyword, $start = 0, $limit = 10)
    {
        $data = [
            'ids' => $ids,
            'keyword' => $keyword,
            'start' => $start,
            'limit' => $limit,
        ];
        return $this->httpGet('wxaapi/newtmpl/getpubtemplatetitles', $data);
    }

    /**
     * 获取模板标题下的关键词库
     * 本接口用于获取小程序订阅消息模板库中某个模板标题下关键词库
     * @param string|int $tid 模板标题 id，可通过接口获取
     * @return type
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getPubTemplateKeyWordsById.html
     */
    public function getPubTemplateKeywords($tid)
    {
        return $this->httpGet('wxaapi/newtmpl/getpubtemplatekeywords', ['tid' => $tid]);
    }

    /**
     * 组合模板并添加到个人模板库
     * 本接口用于组合模板并添加至帐号下的个人模板库，得到用于发消息的模板
     *
     * @param array $params [
     *      tid 模板标题 id，可通过接口获取，也可登录小程序后台查看获取
     *      kidList  开发者自行组合好的模板关键词列表，关键词顺序可以自由搭配（例如 [3,5,4] 或 [4,5,3]），最多支持5个，最少2个关键词组合
     *      sceneDesc 服务场景描述，15个字以内
     * ]
     * @return mixed
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.addTemplate.html
     */
    public function addTemplate($parasm)
    {
        return $this->httpPost('wxaapi/newtmpl/addtemplate', $parasm);
    }

    /**
     * 获取帐号下的模板列表
     * 本接口用于获取小程序帐号下的个人模板库中已经存在的模板列表
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getTemplateList.html
     */
    public function getTemplate()
    {
        return $this->httpGet('wxaapi/newtmpl/gettemplate');
    }

    /**
     * 删除帐号下的某个模板
     * @param string $priTmplId 要删除的模板id
     * @return type
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.deleteTemplate.html
     */
    public function delTemplate($priTmplId)
    {
        return $this->httpPost('wxaapi/newtmpl/deltemplate', ['priTmplId' => $priTmplId]);
    }

    /**
     * @param $openid
     * @param $template_id
     * @param $data
     * @param null $page
     *
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html
     */
    public function send($openid, $template_id, $data, $page = '')
    {
        $postData = [
            'touser' => $openid,
            'template_id' => $template_id,
            'page' => $page,
            'data' => $data,
        ];
        return $this->httpPost('cgi-bin/message/subscribe/send', $postData);
    }
}
