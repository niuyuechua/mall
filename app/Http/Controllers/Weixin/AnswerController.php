<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnswerController extends Controller
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
                            <Content><![CDATA[欢迎关注'.$userInfo['nickname'].']]></Content>
                       </xml>';
                }
            }
        }elseif($msg_type=='text'){

        }
    }
    //创建菜单
    public function createMent(){
        //接口数据
        $post_arr=[
            'button' => [
                [
                    'type'=>'click',
                    'name'=>'答题',
                    'key'=>'answer',
                ],
                [
                    'type'=>'click',
                    'name'=>'我的成绩',
                    'key'=>'grade',
                ],
            ],
        ];
        $json_str=json_encode($post_arr, JSON_UNESCAPED_UNICODE);   //加参数二可处理含中文的数组
        //dd($json_str);die;
        $url= 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.getAccessToken();
        //请求接口
        $client= new Client();
        $responce=$client->request('POST',$url,[
            'body'=>$json_str
        ]);
        //处理响应
        $res_str=$responce->getBody();
        $arr=json_decode($res_str,true);
        dump($arr);
    }
}
