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
//支付
Route::get('pay/test','Weixin\WxPayController@test');


//首页（微店）
Route::get('index/index','IndexController@index');
Route::get('index/register','IndexController@register');
Route::post('index/regDo','IndexController@regDo');
Route::post('index/regAdd','IndexController@regAdd');
Route::get('index/login','IndexController@login');
Route::post('index/loginDo','IndexController@loginDo');
Route::get('index/fenxiao','IndexController@fenxiao');
//所有商品
Route::get('goods/index','GoodsController@index');
Route::get('goods/goodsInfo/{id}','GoodsController@goodsInfo');
Route::post('goods/cartAdd','GoodsController@cartAdd');
//购物车
Route::get('cart/index','CartController@index');
Route::post('cart/checkNum','CartController@checkNum');
Route::post('cart/total','CartController@total');
Route::get('cart/pay','CartController@pay');
Route::get('cart/submitOrder','CartController@submitOrder');
Route::get('cart/success','CartController@success');
Route::get('cart/payment/{order_no}','CartController@payment');
Route::get('cart/paySucc','CartController@paySucc');
Route::post('cart/returnPay','CartController@returnPay');
//个人中心（我的）
Route::get('user/index','UserController@index');
Route::get('user/address','UserController@address');
Route::get('user/areaInfo','UserController@areaInfo');
Route::get('user/order','UserController@order');
Route::get('user/quan','UserController@quan');
Route::get('user/addressEdit','UserController@addressEdit');
Route::post('user/addressAdd','UserController@addressAdd');
Route::get('user/collect','UserController@collect');
Route::get('user/tixian','UserController@tixian');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('reg/reg','RegController@reg');
Route::post('reg/regDo','RegController@regDo');
Route::post('reg/regAdd','RegController@regAdd');
Route::get('reg/login','RegController@login');
Route::post('reg/loginDo','RegController@loginDo');
Route::get('reg/user','RegController@user');



