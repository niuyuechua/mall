<?php

namespace App\Http\Controllers\crontab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OrderModel;

class CrontabController extends Controller
{
    public function del(){
        $order=OrderModel::all();
        foreach($order as $k=>$v){
            if(time()-$v['add_time']>1800&&$v['pay_time']==0){
                $res=OrderModel::where(['oid'=>$v['oid']])->update(['is_del'=>1]);
                echo $res;
            }
        }
    }
}
