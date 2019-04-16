<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;

class WxController extends Controller
{
    //首次接入（get方式）
    public function valid(){
        echo $_GET['echostr'];
    }
    //微信服务器推送通知
    public function wxEvent(){
        //接收微信服务器推送通知
        $content=file_get_contents('php://input');      //xml数据
        $time=date('Y-m-d H:i:s');
        $str=$time.$content."\n";
        file_put_contents('logs/wx_event.log',$str,FILE_APPEND);
        echo 'SUCCESS';
    }
    public function test(){
        $token=$this->getAccessToken();
        echo $token;
    }
    //获取access_token
    public function getAccessToken(){
        $key="set_access_token";
        $data=Redis::get($key);
        if($data){
            echo "有缓存";
        }else{
            echo "没有缓存";
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb6e65a6dbd6cfb06&secret=9fdf084e4ff69341e638e2e7941e8ce8 ";
            $response=file_get_contents($url);      //json字符串
            $arr=json_decode($response,true);       //将json字符串转化成数组
            //做缓存
            Redis::set($key,$arr['access_token']);
            Redis::expire($key,30); //设置时间（30分钟）
            $data=$arr['access_token'];
        }
        return $data;
    }
}
