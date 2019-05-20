<?php

namespace App\Http\Controllers\Weixin;

use App\WxUserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GoodsModel;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class ExamController extends Controller
{
    public function valid(){
        echo $_GET['echostr'];
    }
    public function wxEvent(){
        $content=file_get_contents("php://input");
        $time=date("Y-m-d H:i:s");
        $str=$time.$content.'\n';
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        $obj=simplexml_load_string($content);
        $pb_id=$obj->ToUserName;
        $openid=$obj->FromUserName;
        $msg_type=$obj->MsgType;
        $event=$obj->Event;
        if($msg_type=='event'){
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
        }elseif($msg_type=='text'){
            $content=(string)$obj->Content;
            //dump($content);die;
            $key=$content;
            $goods_name=Redis::get($key);
            if(!$goods_name){
                Redis::set($key,$content);
                Redis::expire($key, 7200);
                $goods_name=$content;
            }
            $data=GoodsModel::where(['goods_name'=>$goods_name])->first()->toArray();
            $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->getAccessToken();
            $post_data='{
                           "touser":"'.$openid.'",
                           "template_id":"t8BraLJvDlgNj1FikjLI9Ew2IJMk6fiplxOtjLP-qgg",
                           "url":"https://pvp.qq.com/",          
                           "data":{                                
                                   "goods_name":{
                                       "value":"'.$data['goods_name'].'",
                                       "color":"#173177"
                                   },
                                   "goods_price": {
                                       "value":"'.$data['goods_price'].'",
                                       "color":"#173177"
                                   },
                                   "create_time": {
                                       "value":"'.date('Y-m-d H:i',$data['create_time']).'",
                                       "color":"#173177"
                                   }
                           }
                       }';
            $client=new Client();
            $res=$client->request('POST',$url,[
                'body'=>$post_data
            ]);
//            $json_res=$res->getBody();
//            $arr_res=json_decode($json_res,true);
//            dump($arr_res);
        }
    }
    public function getAccessToken(){
        $key="access_token";
        $data=Redis::get($key);
        if($data){
            //echo "有缓存";
        }else {
            //echo "无缓存";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb6e65a6dbd6cfb06&secret=9fdf084e4ff69341e638e2e7941e8ce8";
            $res = file_get_contents($url);
            $arr = json_decode($res, true);
            Redis::set($key, $arr['access_token']);
            Redis::expire($key, 7200);
            $data=$arr['access_token'];
        }
        return $data;
    }
}
