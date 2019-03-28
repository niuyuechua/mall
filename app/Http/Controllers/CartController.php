<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Good;
use App\Address;
use App\Order;
use App\OrderDetail;
use App\OrderAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    //购物车列表展示
    public function index(){
        $data=DB::select("select * from goods join cart on goods.goods_id=cart.goods_id and cart_status=1");
        $count=count($data);
        //dd($data);
        return view('cart/index',compact('data','count'));
    }
    //点击加、减号，输入框内容改变
    public function checkNum(){
        $goods_id=request()->input('goods_id');
        $cart_id=request()->input('cart_id');
        $buy_num=request()->input('buy_num');
        $model=new Good;
        $info=$model::where('goods_id',$goods_id)->first()->toArray();
        //dd($info);
        if($buy_num>$info['goods_num']){
            $buy_num=$info['goods_num'];
            $price=$info['goods_price']*$info['goods_num'];
            $arr=[
                'font'=>'库存不足',
                'code'=>2,
                'price'=>$price
            ];
            echo json_encode($arr);
        }else{
            $price=$info['goods_price']*$buy_num;
        }
        $cart_model=new Cart;
        $where=[
            'cart_id'=>$cart_id
        ];
        $data=$cart_model::where($where)->first();
        //dd($data);
        $data->buy_num=$buy_num;
        $res=$data->save();
        if($res){
            $arr=[
            'font'=>'操作成功',
            'code'=>1,
            'price'=>$price
            ];
            echo json_encode($arr);
        }
    }
    //获取总价
    public function total(){
        $cart_id=request()->input('cart_id');
        $cart_id=explode(',',$cart_id);
        //dd($cart_id);
        $model=new Cart;
        /* $where=[
            'cart_id'=>['in',$cart_id]
        ];
        $data=$model::where($where)->get()->toArray(); */
        $data=$model->find($cart_id)->toArray();
        //dd($data);
        $goods_model=new Good;
        $totalPrice=0;
        foreach($data as $k=>$v){
            $goods_id=$v['goods_id'];
            $goods_price=$goods_model::where('goods_id',$goods_id)->first()->goods_price;
            //dd($goods_price);
            $totalPrice+=$v['buy_num']*$goods_price;
        }
        echo $totalPrice;
    }
    //结算
    public function pay(){
        $goodsid=request()->input('goods_id');
        //dd($goodsid);
        if(empty($goodsid)){
            return redirect('cart/index')->with('message','请选择商品');
        }
        $user_id=session('user')['user'];
        $goods_id=explode(',',$goodsid);
        $model=new Good;
        $data=$model->find($goods_id);
        //dd($data);
        $data=$data->toArray();
        $cart_model=new Cart;
        $totalPrice=0;
        foreach($data as $k=>$v){
            $cartWhere=[
            'goods_id'=>$v['goods_id'],
            'user_id'=>$user_id
            ];
            $info=$cart_model::where($cartWhere)->first();
            //dd($info);
            if(empty($info)){
                return redirect('cart/index')->with('message','订单已提交或商品已下架');
            }
            $data[$k]['buy_num']=$info->buy_num;
            $data[$k]['created_at']=$info->created_at;
            $totalPrice+=$v['goods_price']*$info->buy_num;
        }

        //获取默认收货地址
        $ad_model=new Address;
        $address=$ad_model::where('default',1)->first()->toArray();
        //dd($address);
        //获取省名称
        $pid=$address['province'];
        //      >!!>> $province数据类型为数组包结果集 <<!!<
        $province=DB::select('select * from area where id = ?', [$pid])[0]->name;
        $address['province']=$province;
        //获取市名称
        $cid=$address['city'];
        $city=DB::select('select * from area where id = ?', [$cid])[0]->name;
        $address['city']=$city;
        //获取区县名称
        $aid=$address['area'];
        if($aid==''){
            $address['area']='';
        }else{
            $area=DB::select('select * from area where id = ?', [$aid])[0]->name;
            $address['area']=$area;
        }              
        //dd($address);

        return view('cart/pay',compact('data','totalPrice','address','goodsid'));
    }
    //提交订单
    public function submitOrder(){
        $address_id=request()->input('address_id');
        $goods_id=request()->input('goods_id');

        //添加订单表
        $pay=request()->input('pay');
        $price=request()->input('price');
        $goods_id=explode(',',$goods_id);
        $price=ltrim($price,'￥');
        $user_id=session('user')['user'];
        $code=time().rand(1000,9999);
        $model=new Order;
        $data=[
            'order_no'=>$code,
            'user_id'=>$user_id,
            'order_price'=>$price,
            'pay_type'=>$pay,
            'create_time'=>date('Y-m-d H:i:s'),
            'update_time'=>date('Y-m-d H:i:s')
        ];
        $res1=$model->insert($data);
        $order_id=DB::getPdo()->lastInsertId($res1);
    
        //添加订单详情表
        $goods_model=new Good;
        $goodsInfo=$goods_model->find($goods_id)->toArray();
        $cart_model=new Cart;
        $res2='';
        foreach($goodsInfo as $k=>$v){
            $cartWhere=[
            'goods_id'=>$v['goods_id'],
            'user_id'=>$user_id
            ];
            $info=$cart_model::where($cartWhere)->first();
            //dd($info);
            $detail=[
                'order_id'=>$order_id,
                'user_id'=>$user_id,
                'goods_id'=>$v['goods_id'],
                'buy_num'=>$info->buy_num,
                'goods_price'=>$v['goods_price'],
                'goods_name'=>$v['goods_name'],
                'goods_img'=>$v['goods_img'],
                'create_time'=>date('Y-m-d H:i:s'),
                'update_time'=>date('Y-m-d H:i:s')
            ];
            $detail_model=new OrderDetail;
            $res2=$detail_model->insert($detail);         
        }      

        //添加订单地址表
        $ad_model=new Address;
        $address=$ad_model::where('id',$address_id)->first();
        //dd($address->user_name);
        $address=[
            'order_id'=>$order_id,
            'user_id'=>$user_id,
            'user_name'=>$address->user_name,
            'user_tel'=>$address->tel,
            'address_detail'=>$address->detail,
            'province'=>$address->province,
            'city'=>$address->city,
            'area'=>$address->area,
            'create_time'=>date('Y-m-d H:i:s'),
            'update_time'=>date('Y-m-d H:i:s')
        ];
        $address_model=new OrderAddress;
        $res3=$address_model->insert($address);
        
        //修改商品表库存
        $res4='';
        foreach($goodsInfo as $k=>$v){
            $cartWhere=[
                'goods_id'=>$v['goods_id'],
                'user_id'=>$user_id
            ];
            $info=$cart_model::where($cartWhere)->first();
            $goods=$goods_model::where('goods_id',$v['goods_id'])->first();
            $goods->goods_num=$v['goods_num']-$info->buy_num;
            $res4=$goods->save();
        }

        //删除购物车表中已提交订单的商品
        $res5='';
        foreach($goodsInfo as $k=>$v){
            $cartWhere=[
                'goods_id'=>$v['goods_id'],
                'user_id'=>$user_id
            ];
            $res5=$cart_model::where($cartWhere)->delete();
        }

        if($res1&&$res2&&$res3&&$res4&&$res5){
            $arr=[
                'font'=>'提交成功',
                'code'=>1,
                'order_id'=>$order_id
            ];
            echo json_encode($arr);
        }
    }
    //订单提交成功
    public function success(){
        $order_id=request()->input('order_id');
        //dd($order_id);
        $model=new Order;
        $data=$model::where('order_id',$order_id)->first();
        //dd($data);
        return view('cart/success',compact('data'));
    }
    //pc端支付
    /* public function payment($order_no){
        //dd($order_no);
        if(!$order_no){
            return redirect('/cart/success')->with('message','无此订单信息');
        }
        $model=new Order;
        $order_price=$model->where('order_no',$order_no)->value('order_price');
        //dd($order_price);
        if($order_price<=0){
            return redirect('/cart/success')->with('message','此订单无效');
        }
        //app_path  生成相对于app目录的绝对路径，并返回绝对路径+参数路径
        require_once app_path('libs/alipay/pagepay/service/AlipayTradeService.php');
        require_once app_path('libs/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php');
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = trim($order_no);

        //订单名称，必填
        $subject = '1809a支付';

        //付款金额，必填
        $total_amount = trim($order_price);

        //商品描述，可空
        $body = '1809a支付';

        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AlipayTradeService(config('alipay')); */

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
        */
        /* $response = $aop->pagePay($payRequestBuilder,config('alipay.return_url'),config('alipay.notify_url'));

        //输出表单
        var_dump($response);
    } */
    //手机版支付
    public function payment($order_no){
        //dd($order_no);
        require_once app_path('libs/alipay.trade.wap.pay/wappay/service/AlipayTradeService.php');
        require_once app_path('libs/alipay.trade.wap.pay/wappay/buildermodel/AlipayTradeWapPayContentBuilder.php'); 
        if(!$order_no){
            return redirect('/cart/success')->with('message','无此订单信息');
        }
        $model=new Order;
        $order_price=$model->where('order_no',$order_no)->value('order_price');
        //dd($order_price);
        if($order_price<=0){
            return redirect('/cart/success')->with('message','此订单无效');
        }
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $order_no;

        //订单名称，必填
        $subject = '1809a测试';

        //付款金额，必填
        $total_amount = $order_price;

        //商品描述，可空
        $body = '1809a测试';

        //超时时间
        $timeout_express="1m";

        $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);

        $payResponse = new \AlipayTradeService(config('alipay'));
        $result=$payResponse->wapPay($payRequestBuilder,config('alipay.return_url'),config('alipay.notify_url'));

        return ;
    }
    //支付同步通知
    public function paySucc(){
        //dd($_GET);
        $out_trade_no=trim($_GET['out_trade_no']);
        $total_amount=trim($_GET['total_amount']);
        /* var_dump($out_trade_no);
        var_dump($total_amount);
        var_dump($_GET['seller_id']);
        var_dump(config('alipay.seller_id'));
        var_dump($_GET['app_id']);
        var_dump($_GET['app_id']);exit; */
        $data=DB::table('order')->where(['order_no'=>$out_trade_no,'order_price'=>$total_amount])->first();
        //dd($data);
        /* if(!$data){
            return redirect('/user/order')->with('message','无此订单信息');
        } */
        if(trim($_GET['seller_id'])!=config('alipay.seller_id') || trim($_GET['app_id'])!=config('alipay.app_id')){
            return redirect('/user/order')->with('message','支付出现错误');
        }
        return redirect('/user/order');
    }
    //支付异步通知
    public function returnPay(){
        //dd($_POST);
        $post=json_encode($_POST);
        Log::channel('pay')->info($post);
        /* require_once app_path('libs/alipay.trade.wap.pay/wappay/service/AlipayTradeService.php');
        $arr=$_POST;
        $alipaySevice = new \AlipayTradeService(config('alipay')); 
        $result = $alipaySevice->check($arr);
        dd($result); */
        //$result=true;
        $out_trade_no=trim($_POST['out_trade_no']);
        $total_amount=trim($_POST['total_amount']);
        $data=DB::table('order')->where(['order_no'=>$out_trade_no,'order_price'=>$total_amount])->first();
        if(!$data){
            Log::channel('pay')->info($post.'无此订单信息');exit;
        }
        if(trim($_POST['seller_id'])!=config('alipay.seller_id') || trim($_POST['app_id'])!=config('alipay.app_id')){
            Log::channel('pay')->info($post.'支付出现错误');exit;
        }
        //更改订单状态、减少库存
        echo "success";	
        /* if($result) {//验证成功
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            
            //商户订单号   
            $out_trade_no = $_POST['out_trade_no'];   
            //支付宝交易号    
            $trade_no = $_POST['trade_no'];     
            //交易状态
            $trade_status = $_POST['trade_status'];         

            if($_POST['trade_status'] == 'TRADE_FINISHED') {      
                //判断该笔订单是否在商户网站中已经做过处理
                    //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                    //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                    //如果有做过处理，不执行商户的业务程序
                        
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                    //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                    //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                    //如果有做过处理，不执行商户的业务程序		

                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——          
            echo "success";		//请不要修改或删除          
        }else {
            //验证失败
            echo "fail";	//请不要修改或删除
        } */
    }
}
