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
Route::get('wx/getAccessToken','Weixin\WxController@getAccessToken');
Route::get('wx/test','Weixin\WxController@test');
Route::post('wx/valid','Weixin\WxController@wxEvent');
Route::get('wx/goodsDetail/{goods_id}','Weixin\WxController@goodsDetail');
//自定义菜单
Route::get('wx/createMent','Weixin\WxController@createMent');
//支付
Route::get('pay/test','Weixin\WxPayController@test');
Route::post('pay/notice','Weixin\WxPayController@notice');