<?php

namespace App\Http\Controllers\Weixin;

use App\WxUserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExamController extends Controller
{
    public function wxEvent(){
        $content=file_get_contents("php://input");
        $time=date("Y-m-d H:i:s");
        $str=$time.$content.'\n';
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        $obj=simplexml_load_string($content);
        $pb_id=$obj->ToUserName;
        $openid=$obj->FromUserName;
        $event=$obj->Event;
        if($event=="subscribe"){
            $userInfo=WxUserModel::where(['openid'=>$openid])->first();
            if($userInfo){
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$pb_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA['.'欢迎回来'.$userInfo['nickname'].']]></Content>
                       </xml>';
            }else{
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$pb_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[请输入商品名称]]></Content>
                       </xml>';
            }
        }
    }
    public function getAccessToken(){
        $key="access_token";
        $data=Redis::get($key);
        if($data){
            echo "有缓存";
        }else {
            echo "无缓存";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb6e65a6dbd6cfb06&secret=9fdf084e4ff69341e638e2e7941e8ce8";
            $res = file_get_contents($url);
            $arr = json_decode($res, true);
            Redis::set($key, $arr['access_token']);
            Redis::expire($key, 7200);
            $data=$arr['access_token'];
        }
        echo $data;
    }
}
