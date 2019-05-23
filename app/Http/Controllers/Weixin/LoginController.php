<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BindModel;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    public function index(){
        return view('login.login');
    }
    //PC微信扫码页面
    public function scan(){
        $random=time().rand(1000,9999);
        $text='http://www.nyc666666.top/login/doScan?id='.$random;
        return view('login.scan',compact('text','random'));
    }
    //手机微信扫码
    public function doScan(){
        $id=$_GET['id'];
        $openid=getOpenid();
        Cache::put($id,$openid,180);
        return '扫码授权成功，请等待服务器响应...';
    }
    //检测是否扫描
    public function checkScan(){
        $id=request()->id;
        $openid=Cache::get($id);
        if($openid){
            //已扫描
            $data=BindModel::where(['openid'=>$openid])->first();
            if($data){
                return json_encode(['code'=>1,'msg'=>'登录成功']);
            }else{
                return json_encode(['code'=>2,'msg'=>'请先绑定微信账号']);
            }
        }
    }
    public function bind(){
        getOpenid();        //微信网页授权 获取openid（封装=下面两个方法）
//        $openid=session('openid');
//        //dump($openid);die;
//        if(empty($openid)){
//            //1、跳转到微信服务器 授权
//            $redirect_url=urlEncode("http://www.nyc666666.top/login/getOpenid");
//            $url2="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('WX_APP_ID')."&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
//            header("location:$url2");
//        }
        return view('login.bind');
    }
//    //2、微信授权回调
//    public function getOpenid(){
//        $code=$_GET['code'];
//        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('WX_APP_ID')."&secret=".env('WX_APP_SEC')."&code=".$code."&grant_type=authorization_code";
//        $res=json_decode(file_get_contents($url),true);
//        $openid=$res['openid'];
//        session(['openid'=>$openid]);
//        return redirect('login/bind');
//    }
    public function doBind(){
        $data=request()->all();
        $openid=session('openid');
        $data['openid']=$openid;
        $res=BindModel::insert($data);
        if($res){
            echo '绑定成功';
        }else{
            echo '绑定失败';
        }
    }
    public function sendCode(){
        $name=$_GET['name'];
        $pwd=$_GET['pwd'];
        $openid=BindModel::where(['name'=>$name,'pwd'=>$pwd])->value('openid');
        if(empty($openid)){
            echo 0;die;
        }
        $code=rand(1000,9999);
        setcookie($code,$code,time()+300);
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".getAccessToken();
        $post_data='{
           "touser":"'.$openid.'",
           "template_id":"mBabFJcP_iWKGZ9TqdI4rbpe8r1z3ADG7K5lGAm_kQQ",
           "url":"https://pvp.qq.com/",          
           "data":{
                "code": {
                    "value":"'.$code.'",
                       "color":"#173177"
                   },
                   "date":{
                    "value":"'.date('Y-m-d H:i',time()).'",
                       "color":"#173177"
                   }
           }
        }';
        $client=new Client();
        $res=$client->request('POST',$url,[
            'body'=>$post_data
        ]);
        $json_res=$res->getBody();
        $arr_res=json_decode($json_res,true);
        //dump($arr_res);
        if($arr_res['errcode']==0){
            echo 1;
        }else{
            echo 2;
        }
    }
    public function doLogin(){
        $data=request()->all();
        $user=BindModel::where(['name'=>$data['name']])->first();
        if($user){
            if($data['pwd']!=$user->pwd){
                echo '用户名或密码错误，请重新登录';
                header("refresh:3;url=/login");die;
            }
        }else{
            echo '用户名或密码错误，请重新登录';
            header("refresh:3;url=/login");die;
        }
        $code=$_COOKIE;
        //dump($code);
        if(!in_array($data['code'],$code)){
            echo '验证码错误或已过期，请重新获取';
            header("refresh:3;url=/login");die;
        }
        echo '登录成功，正在跳转至后台首页';
        header("refresh:2;url=/admin");
    }
}
