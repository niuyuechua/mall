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
        $str=$time.$content."\n";
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
                    $str="欢迎回来".$userInfo['nickname'];
                    $this->sendTextMsg($openid,$str);
                }else{
                    $str="欢迎关注".$userInfo['nickname'];
                    $this->sendTextMsg($openid,$str);
                }
            }elseif($event=="CLICK"){
                $eventKey=$obj->EventKey;
                if($eventKey=='answer'){
                    $data=AnswerModel::get()->toArray();
                    $key=array_rand($data,1);
                    $answer=$data[$key];
                    UserAnswerModel::insert(['openid'=>$openid,'id'=>$answer['id']]);
                    $str=$answer['topic']."\n"."A：".$answer['answer_A']."  B：".$answer['answer_B'];
                    $this->sendTextMsg($openid,$str);
                }elseif($eventKey=='grade'){
                    $correct=UserAnswerModel::where(['openid'=>$openid,'true'=>1])->count();
                    $error=UserAnswerModel::where(['openid'=>$openid,'true'=>2])->count();
                    $str="您共答对".$correct."道题，答错".$error."道";
                    $this->sendTextMsg($openid,$str);
                }
            }
        }elseif($msg_type=='text'){
            $content=$obj->Content;
            $last=UserAnswerModel::orderby('a_id','desc')->first();
            if(empty($last)){
                $str="请先点击答题";
                $this->sendTextMsg($openid,$str);
            }
            if($last['answer']==''&&$last['true']==''){
                $correct=AnswerModel::where(['id'=>$last['id']])->value('correct');
                $last['answer']=$content;
                if($content==$correct){
                    $last['true']=1;
                    $str="回答正确";
                }else{
                    $last['true']=2;
                    $str="回答错误";
                }
                $last->save();
                $this->sendTextMsg($openid,$str);
            }else{
                $str="该题目已回答，请重新抽题";
                $this->sendTextMsg($openid,$str);
            }
        }
    }
    public function sendTextMsg($openid,$str){
        echo '<xml>
                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                  <FromUserName><![CDATA[gh_3174a6d2a0ac]]></FromUserName>
                  <CreateTime>'.time().'</CreateTime>
                  <MsgType><![CDATA[text]]></MsgType>
                  <Content><![CDATA['.$str.']]></Content>
                </xml>';
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
