<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/info',function(){
    phpinfo();
});

//微信公众平台
Route::get('wx/valid','Weixin\WxController@valid');
Route::post('wx/valid','Weixin\WxController@wxEvent');
Route::get('wx/getAccessToken','Weixin\WxController@getAccessToken');
Route::get('wx/test','Weixin\WxController@test');
//Route::get('wx/valid','Weixin\LoveController@valid');       //表白墙
//Route::post('wx/valid','Weixin\LoveController@wxEvent');        //表白墙
//月考机试
//Route::post('wx/valid','Weixin\ExamController@wxEvent');
//Route::get('wx/getAccessToken','Weixin\ExamController@getAccessToken');
//图文消息详情
Route::get('wx/goodsDetail','Weixin\WxController@goodsDetail');
Route::get('wx/phoneDetail','Weixin\WxController@phoneDetail');
//自定义菜单
Route::get('wx/createMent','Weixin\WxController@createMent');
//Route::get('wx/createMent','Weixin\LoveController@createMent');     //表白墙
//支付
Route::get('pay/test','Weixin\WxPayController@test');
Route::post('pay/notice','Weixin\WxPayController@notice');
//计划任务
Route::get('crontab/del','crontab\CrontabControllr@del');
//获取授权后重定向的回调连接地址
Route::get('test/urlencode',function(){
    echo urlEncode($_GET['url']);
});
//微信授权
Route::get('wx/authorization','Weixin\WxController@authorization');
//授权回调
Route::get('wx/getUinfo','Weixin\WxController@getUinfo');
//生成带参数的二维码
Route::get('wx/getTicket','Weixin\WxController@getTicket');
Route::get('wx/code','Weixin\WxController@code');
//自定义后台登录
Route::get('/login','Weixin\LoginController@index');
Route::get('login/getOpenid','Weixin\LoginController@getOpenid');
Route::get('login/bind','Weixin\LoginController@bind');
Route::get('login/doBind','Weixin\LoginController@doBind');
Route::get('login/sendCode','Weixin\LoginController@sendCode');
Route::post('login/doLogin','Weixin\LoginController@doLogin');
Route::get('login/scan','Weixin\LoginController@scan');
Route::get('login/doScan','Weixin\LoginController@doScan');
Route::get('login/checkScan','Weixin\LoginController@checkScan');
//计划任务 定时群发
Route::get('wx/plan','Weixin\WxController@plan');