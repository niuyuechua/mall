<?php
namespace App\Helper2;
use Illuminate\Support\Facades\Redis;

//此类与Helper文件夹中functions.php文件中的 函数 作用相同。只不过是调用的方法不同，Helper是直接写函数名调用；Helper2是
//先 use App\Helper2\Functions; 然后用 类名::方法名 调用。

class Functions
{
    //获取access_token
    public static function getAccessToken()
    {
        $key = 'wx_access_token';
        $access_token = Redis::get($key);
        if ($access_token) {
            return $access_token;
        } else {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . env('WX_APP_ID') . "&secret=" . env('WX_APP_SEC') . "";
            $response = json_decode(file_get_contents($url), true);
            if (isset($response['access_token'])) {
                Redis::set($key, $response['access_token']);
                Redis::expire($key, 3600);
                return $response['access_token'];
            } else {
                return false;
            }
        }
    }
    //获取新用户信息
    public static function getUserInfo($openid){
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".Functions::getAccessToken()."&openid=".$openid."&lang=zh_CN";
        $data=file_get_contents($url);
        $u=json_decode($data);
        return $u;
    }
    //获取生成带参数二维码的ticket
    public static function getTicket()
    {
        $key = "wx_jsapi_ticket";
        $ticket = Redis::get($key);
        if ($ticket) {
            return $ticket;
        } else {
            //静态方法调用
            $access_token = Functions::getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $access_token . "&type=jsapi";
            $ticket_info = json_decode(file_get_contents($url), true);
            if (isset($ticket_info['ticket'])) {
                Redis::set($key, $ticket_info['ticket']);
                Redis::expire($key, 7200);
                return $ticket_info['ticket'];
            } else {
                return false;
            }
        }
    }
}