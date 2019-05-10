<?php

namespace App\Http\Controllers;

use App\Area;
use App\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(){
        return view('user/index');
    }
    //获取省份
    public function address(){
        $province=$this->getAddress(0);
        return view('user/address',compact('province'));
    }
    public function getAddress($pid){
        $model=new Area;
        $areaInfo=$model::where('pid',$pid)->get();
        return $areaInfo;
    }
    //获取市、区县
    public function areaInfo(){
        $pid=request()->input('id');
        if($pid==''){
            $arr=[
                'font'=>'请选择省市区',
                'code'=>2
            ];
            echo json_encode($arr);exit;
        }
        $areaInfo=$this->getAddress($pid);
        echo json_encode(['areaInfo'=>$areaInfo,'code'=>1]);
    }
    public function order(){
        return view('user/order');
    }
    public function quan(){
        return view('user/quan');
    }
    //全部收货地址展示
    public function addressEdit(){
        $ad_model=new Address;
        $address=$ad_model->all()->toArray();
        //dd($address);
        foreach($address as $k=>$v){
            //获取省名称
            $pid=$v['province'];
            //      >!!>> $province数据类型为数组包结果集 <<!!<
            $province=DB::select('select * from area where id = ?', [$pid])[0]->name;
            $address[$k]['province']=$province;
            //获取市名称
            $cid=$v['city'];
            $city=DB::select('select * from area where id = ?', [$cid])[0]->name;
            $address[$k]['city']=$city;
            //获取区县名称
            $aid=$v['area'];
            if($aid==''){
                $address[$k]['area']='';
            }else{
                $area=DB::select('select * from area where id = ?', [$aid])[0]->name;
                $address[$k]['area']=$area;
            }            
        }
        //dd($address);
        return view('user/addressEdit',compact('address'));
    }
    //收货地址添加
    public function addressAdd(){
        $data=request()->input();
        //dd($data['default']);
        if($data['default']=='true'){
            $data['default']=1;
        }else{
            $data['default']=0;
        }
        //dd($data);
        unset($data['_token']);
        $model=new Address;
        $res=$model->insert($data);
        if($res){
            $arr=[
                'font'=>'保存成功',
                'code'=>1
            ];
            echo json_encode($arr);
        }
    }
    public function collect(){
        return view('user/collect');
    }
    public function tixian(){
        return view('user/tixian');
    }
}
