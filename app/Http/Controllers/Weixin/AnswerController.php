<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use App\AnswerModel;
use App\UserAnswerModel;

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
            }elseif($event=="CLICK"){
                $eventKey=$obj->EventKey;
                if($eventKey=='answer'){
                    $data=AnswerModel::get()->toArray();
                    $key=array_rand($data,1);
                    $answer=$data[$key];
                    UserAnswerModel::insert(['openid'=>$openid,'id'=>$answer['id']]);
                    echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$pb_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA['.$answer['topic'].''.\n.'A：'.$answer['answer_A'].'  B：'.$answer['answer_B'].']]></Content>
                       </xml>';
                }elseif($eventKey=='grade'){
                    $correct=UserAnswerModel::where(['openid'=>$openid,'true'=>1])->count();
                    $error=UserAnswerModel::where(['openid'=>$openid,'true'=>2])->count();
                    echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$pb_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[您共答对'.$correct.'道题，共打错'.$error.']]></Content>
                       </xml>';
                }
            }
        }elseif($msg_type=='text'){
            $content=$obj->Content;
            $last=UserAnswerModel::orderby('a_id','desc')->first();
            if(empty($last)){
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$pb_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[请先点击答题]]></Content>
                       </xml>';die;
            }
            if($last['answer']==''&&$last['true']==''){
                $correct=AnswerModel::where(['id'=>$last['id']])->value('correct');
                if($content==$correct){
                    UserAnswerModel::orderby('a_id','desc')->update(['answer'=>$content,'true'=>1]);
                    echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$pb_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[回答正确]]></Content>
                       </xml>';
                }else{
                    UserAnswerModel::orderby('a_id','desc')->update(['answer'=>$content,'true'=>2]);
                    echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$pb_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[回答错误]]></Content>
                       </xml>';
                }
            }else{
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$pb_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[该题目已回答，请重新抽题]]></Content>
                       </xml>';
            }
        }
    }
    public function addTopic(){
        return view('answer.addTopic');
    }
    public function doAdd(){
        $data=request()->all();
        unset($data['_token']);
        $res=AnswerModel::insert($data);
        if($res){
            echo '添加成功';
        }else{
            echo '添加失败';
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
