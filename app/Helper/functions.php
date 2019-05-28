<?php
use Illuminate\Support\Facades\Redis;
    //获取access_token
    function getAccessToken(){
        $key='wx_access_token';
        $access_token=Redis::get($key);
        if($access_token){
            return $access_token;
        }else{
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APP_ID')."&secret=".env('WX_APP_SEC')."";
            $response=json_decode(file_get_contents($url),true);
            if(isset($response['access_token'])){
                Redis::set($key,$response['access_token']);
                Redis::expire($key,3600);
                return $response['access_token'];
            }else{
                return false;
            }
        }
    }
    //获取新用户信息
    function getUserInfo($openid){
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".getAccessToken()."&openid=".$openid."&lang=zh_CN";
        $data=file_get_contents($url);
        $u=json_decode($data);
        return $u;
    }
    //获取用于调用微信JS接口的临时票据jsapi_ticket
    function getTicket(){
        $key="wx_jsapi_ticket";
        $ticket=Redis::get($key);
        if($ticket){
            return $ticket;
        }else{
            $access_token=getAccessToken();
            $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
            $ticket_info=json_decode(file_get_contents($url),true);
            if(isset($ticket_info['ticket'])){
                Redis::set($key,$ticket_info['ticket']);
                Redis::expire($key,7200);
                return $ticket_info['ticket'];
            }else{
                return false;
            }
        }
    }
    //微信网页授权 获取openid
    function getOpenid(){
        $openid=session('openid');
        if($openid){
            return $openid;     //（不需要）
        }else{
            $SERVER_NAME = $_SERVER['HTTP_HOST'];  //获取域名
            $REQUEST_URI = $_SERVER['REQUEST_URI']; //获取参数
            $redirect_url=urlEncode('http://'.$SERVER_NAME.$REQUEST_URI);
            $code = request('code');
            if($code){
                //2、微信授权回调
                $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('WX_APP_ID')."&secret=".env('WX_APP_SEC')."&code=".$code."&grant_type=authorization_code";
                $res=json_decode(file_get_contents($url),true);
                $openid=$res['openid'];
                session(['openid'=>$openid]);
                return $openid;     //（不需要）
            }else{
                //1、跳转到微信服务器 授权
                $url2="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('WX_APP_ID')."&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
                header("location:$url2");
            }
        }
    }