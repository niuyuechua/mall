<?php

namespace App\Http\Controllers;

use App\Good;
use App\Cart;
use Illuminate\Http\Request;

class GoodsController extends Controller
{
    public function index(){
        $model=new Good;
        $data=$model->all();
        return view('goods/index',compact('data'));
    }
    public function goodsInfo($id){
        //dd($id);
        $model=new Good;
        $data=$model->find($id)->toArray();
        //dd($data);
        $imgs=rtrim($data['goods_imgs'],'|');
        $imgs=explode('|',$imgs);
        $data['goods_imgs']=$imgs;
        return view('goods/goodsInfo',compact('data'));
    }
    //加入购物车
    public function cartAdd(){
        $user_id=session('user')['user'];
        $goods_id=request()->input('goods_id');
        $buy_num=request()->input('buy_num');
        if($buy_num<1){
            $arr=[
                'font'=>'请至少购买一件',
                'code'=>2
            ];
            echo json_encode($arr);exit;
        }
        //var_dump($buy_num);exit;
        $goods_model=new Good;
        $goods_info=$goods_model::where('goods_id',$goods_id)->first()->toArray();
        if($buy_num>$goods_info['goods_num']){
            $arr=[
                'font'=>'库存不足',
                'code'=>2
            ];
            echo json_encode($arr);exit;
        }
        $model=new Cart;
        $where=[
            'goods_id'=>$goods_id,
            'user_id'=>$user_id
        ];
        $info=$model::where($where)->first();
        //dd($info);
        if($info){
            //var_dump($info->buy_num);exit;
            $info->buy_num=$info->buy_num+$buy_num;
            if($info->buy_num>$goods_info['goods_num']){
                $arr=[
                    'font'=>'库存不足',
                    'code'=>2
                ];
                echo json_encode($arr);exit;
            }
            $res=$info->save();
        }else{
            $data=[
            'goods_id'=>$goods_id,
            'buy_num'=>$buy_num,
            'user_id'=>$user_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s'),
            ];
            $res=$model->insert($data);
            //dd($res);
        }      
        if($res){
            $arr=[
                'font'=>'加入购物车成功,在购物车等你呦~',
                'code'=>1
            ];
            echo json_encode($arr);
        }
    }
}
