<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ActModel;
use App\LoveModel;
use GuzzleHttp\Client;

class LoveController extends Controller
{
    //首次接入（get方式）
    public function valid(){
        echo $_GET['echostr'];
    }

    public function wxEvent(){
        //接收微信服务器推送通知
        $content=file_get_contents('php://input');      //xml数据
        $time=date('Y-m-d H:i:s');
        $str=$time.$content."\n";
        file_put_contents('logs/wx_event.log',$str,FILE_APPEND);
        //echo 'SUCCESS';
        $obj=simplexml_load_string($content);       //将xml数据转化成对象
        //dump($obj);die;
        $kf_id = $obj->ToUserName;  //公众号ID
        $openid=$obj->FromUserName; //用户ID
        $msg_type=$obj->MsgType;    //消息类型（包括事件）
        $event=$obj->Event;    //事件类型（消息类型为事件时，有此字段）
        $eventKey=$obj->EventKey;   //创建click菜单时设置的key值
        if($msg_type=='event'){
            if($event=='CLICK' && $eventKey=='love'){
                $this->act($openid,"我要表白");
                $str="请输入你要表白的人的名字";
                $this->sendTextMsg($openid,$str);
            }elseif($event=='CLICK' && $eventKey=='Looking for love'){
                $this->act($openid,"查看表白");
                $str="请输入要查询的人的名字";
                $this->sendTextMsg($openid,$str);
            }
        }
        $last_act=ActModel::orderBy('act_time','desc')->first();
        $act_time=$last_act->act_time;
        if($msg_type=='text' && time()-$act_time<=60){
            $act_name=$last_act->act_name;
            if($act_name=='我要表白'){
                //根据用户上一步执行的操作，判断本步执行的操作（输入表白人名字）
                $this->act($openid,"输入表白人名字");
                //表白人名字入库
                $arr=[
                    'openid'=>$openid,
                    'name'=>$obj->Content,
                    'content'=>''
                ];
                LoveModel::insert($arr);
                $str="请输入表白内容";
                $this->sendTextMsg($openid,$str);
            }elseif($act_name=='输入表白人名字'){
                //表白内容入库
                $id=LoveModel::orderBy('id','desc')->first()->id;
                $content=$obj->Content;
                LoveModel::where(['id'=>$id])->update(['content'=>$content]);
                $str="表白成功";
                $this->sendTextMsg($openid,$str);
            }elseif($act_name=='查看表白'){
                $name=$obj->Content;
                $data=LoveModel::where(['name'=>$name])->get();
                $num=count($data,1);
                if($num==0){
                    $str="$name 还未被表白";
                }else{
                    $content='';
                    foreach($data as $k=>$v){
                        $content.=$v['content']."\n";
                    }
                    $str="$name 被表白次数：$num"."\n"."表白内容："."\n".$content;
                }
                $this->sendTextMsg($openid,$str);
            }
        }
    }
    //记录上一步动作的返回数据
    public function act($openid,$str){
        $arr=[
            'openid'=>$openid,
            'act_name'=>$str,
            'act_time'=>time()
        ];
        ActModel::insert($arr);
    }
    //回复文本消息
    public function sendTextMsg($openid,$str){
        echo '<xml>
                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                  <FromUserName><![CDATA[gh_3174a6d2a0ac]]></FromUserName>
                  <CreateTime>'.time().'</CreateTime>
                  <MsgType><![CDATA[text]]></MsgType>
                  <Content><![CDATA['.$str.']]></Content>
                </xml>';
    }
    public function createMent()
    {
        //接口数据
        $post_arr = [
            'button' => [
                [
                    'type' => 'click',
                    'name' => '功能说明',
                    'key' => 'function declaration',
                ],
                ['name' => '表白墙',
                    'sub_button' => [
                        [
                            'type' => 'click',
                            'name' => '我要表白',
                            'key' => 'love',
                        ],
                        [
                            'type' => 'click',
                            'name' => '查看表白',
                            'key' => 'Looking for love',
                        ],
                    ],
                ],
            ],
        ];
        $json_str = json_encode($post_arr, JSON_UNESCAPED_UNICODE);   //加参数二可处理含中文的数组
        //dd($json_str);die;
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . getAccessToken();
        //请求接口
        $client = new Client();
        $responce = $client->request('POST', $url, [
            'body' => $json_str
        ]);
        //处理响应
        $res_str=$responce->getBody();
        $arr=json_decode($res_str,true);
        if($arr['errcode']==0){
            echo "创建菜单成功";
        }else{
            echo '创建菜单失败';
        }
    }
}
