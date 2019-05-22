<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BindModel;

class LoginController extends Controller
{
    public function index(){
        getOpenid();        //微信网页授权 获取openid（封装=下面两个方法）
        return view('login.login');
    }
//    public function bind(){
//        $openid=session('openid');
//        //dump($openid);die;
//        if(empty($openid)){
//            //1、跳转到微信服务器 授权
//            $redirect_url=urlEncode("http://www.nyc666666.top/login/getOpenid");
//            $url2="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('WX_APP_ID')."&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
//            header("location:$url2");
//        }
//        return view('login.bind');
//    }
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
}
