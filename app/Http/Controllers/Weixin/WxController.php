<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WxController extends Controller
{
    //微信首次接入（get方式）
    public function valid(){
        echo $_GET['echostr'];
    }
}
