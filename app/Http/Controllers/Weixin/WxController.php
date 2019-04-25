<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\WxUserModel;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use App\GoodsModel;
use App\UserModel;
use Illuminate\Support\Str;

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
        //echo 'SUCCESS';
        $obj=simplexml_load_string($content);       //将xml数据转化成对象
        //dump($obj);die;
        $kf_id = $obj->ToUserName;  //公众号ID
        $openid=$obj->FromUserName; //用户ID
        $event=$obj->Event;    //事件类型
        $msg_type=$obj->MsgType;    //消息类型
        //echo $openid;die;
        $event=$obj->Event;
        if($event=='subscribe'){
            $userInfo=WxUserModel::where('openid','=',"$openid")->first();
            if($userInfo){
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA['.'欢迎回来'.$userInfo['nickname'].']]></Content>
                       </xml>';
            }else{
                //获取新用户信息
                $info = $this->getUserInfo($openid);//对象格式
                //用户信息入库
                $data=[
                    'openid'=>$info->openid,
                    'nickname'=>$info->nickname,
                    'sex'=>$info->sex,
                    'headimgurl'=>$info->headimgurl,
                    'subscribe_time'=>$info->subscribe_time
                ];
                $res = WxUserModel::insert($data);
                echo '<xml>
                            <ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA['.'欢迎关注'. $info->nickname .']]></Content>
                          </xml>';
            }
        }
        if($msg_type=='text'){
            if($obj->Content=='最新商品'){
                $goods=GoodsModel::orderby('create_time','desc')->limit(5)->get()->toArray();
                //$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->getAccessToken()."&type=image";
                foreach($goods as $k=>$v){
                    $img=$v['goods_img'];
                    $picurl="http://1809niuyuechyuang.comcto.com/goodsimg/".$img;
                    $res='<xml>
                          <ToUserName><![CDATA['.$openid.']]></ToUserName>
                          <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                          <CreateTime>'.time().'</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA[最新商品]]></Title>
                              <Description><![CDATA[啦啦啦]]></Description>
                              <PicUrl><![CDATA['.$picurl.']]></PicUrl>
                              <Url><![CDATA[http://1809niuyuechyuang.comcto.com/wx/goodsDetail?goods_id='.$v['goods_id'].']]></Url>
                            </item>
                          </Articles>
                        </xml>';
                    echo $res;
                }

            }
        }
    }
    //商品详情
    public function goodsDetail(){
        $goods_id=$_GET['goods_id'];
        //echo $goods_id;die;
        $goods=GoodsModel::where(['goods_id'=>$goods_id])->first()->toArray();
        $js_config=$this->getConfig();
        $data=[
            'goods'=>$goods,
            'js_config'=>$js_config
        ];
        return view('goods.detail',$data);
    }
    //获取config接口参数值
    public function getConfig(){
        //获取生成签名的参数
        $ticket=getTicket();
        //echo $ticket;
        $nonceStr=Str::random(10);
        $timestamp=time();
        $current_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        //echo $current_url;die;
        //生成签名
        $string1="jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$current_url";
        //echo $string1;die;
        $signature=sha1($string1);
        //echo $signature;

        $js_config=[
            'appId'=>env('WX_APP_ID'),  //公众号APPID
            'timestamp'=>$timestamp,    //时间戳
            'nonceStr'=>$nonceStr,     //随机字符串
            'signature'=>$signature,    //签名
        ];
        return $js_config;
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
            Redis::expire($key,3600); //设置时间（30）
            $data=$arr['access_token'];
        }
        return $data;
    }
    //获取新用户信息
    public function getUserInfo($openid){
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->getAccessToken()."&openid=".$openid."&lang=zh_CN";
        $data=file_get_contents($url);
        $u=json_decode($data);
        return $u;
    }

    //菜单
    public function createMent(){
        //url
        $url= 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getAccessToken();
        //dump($url);die;

        //接口数据
        $post_arr=[
            'button' => [
                [
                    'type'=>'click',
                    'name'=>'今日歌曲',
                    'key'=>'V1001_TODAY_MUSIC',
                ],
                [
                    'type'=>'click',
                    'name'=>'1809A',
                    'key'=>'V1001_TODAY_MUSIA',
                ],
            ],
        ];
        $json_str=json_encode($post_arr, JSON_UNESCAPED_UNICODE);
        //dd($json_str);die;
        //请求接口
        $client= new Client();
        $responce=$client->request('POST',$url,[
            'body'=>$json_str
        ]);
        //dd($responce);die;
        //处理响应
        $res_str=$responce->getBody();
        echo $res_str;
        //判断错误信息
//        if($res_str>['errcode']>0){
//
//        }else{
//            echo '错误';
//        }
    }

    //微信网页授权
    public function authorization(){
        //$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb6e65a6dbd6cfb06&redirect_uri=http%3A%2F%2F1809niuyuechyuang.comcto.com%2Fwx%2FgetUinfo&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
    }
    //授权回调
    public function getUinfo(){
        echo '<pre>';print_r($_GET);echo '</pre>';
        $code=$_GET['code'];
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('WX_APP_ID')."&secret=".env('WX_APP_SEC')."&code=".$code."&grant_type=authorization_code";
        $res=json_decode(file_get_contents($url),true);
        echo '<pre>';print_r($res);echo '</pre>';
        $access_token=$res['access_token'];
        $openid=$res['openid'];
        $url2="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $userInfo=json_decode(file_get_contents($url2),true);
        echo '<pre>';print_r($userInfo);echo '</pre>';
        //用户信息入库
//        $userInfo=UserModel::where('openid','=',$userInfo['openid'])->first();
//        if($userInfo){
//            echo '欢迎回来 '.$userInfo['nickname'];
//        }else{
//            //用户信息入库
//            $res = UserModel::insert($userInfo);
//            echo '欢迎关注 '.$userInfo['nickname'];
//        }
    }
}
