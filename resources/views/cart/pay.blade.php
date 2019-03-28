<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Author" contect="http://www.webqin.net">
    <title>三级分销</title>
    <link rel="shortcut icon" href="/index/images/favicon.ico" />
    
    <!-- Bootstrap -->
    <link href="/index/css/bootstrap.min.css" rel="stylesheet">
    <link href="/index/css/style.css" rel="stylesheet">
    <link href="/index/css/response.css" rel="stylesheet">
    <script src="/index/layui/layui.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="maincont">
     <header>
      <a href="javascript:history.back(-1)" class="back-off fl"><span class="glyphicon glyphicon-menu-left"></span></a>
      <div class="head-mid">
       <h1>购物车</h1>
      </div>
     </header>
     <div class="head-top">
      <img src="/index/images/head.jpg" />
     </div><!--head-top/-->
     <div class="dingdanlist">
      <table>
       <tr>
        <td class="dingimg" width="75%" colspan="2"><a href="/user/addressEdit" style="color:red">>>添加、修改收货地址<<</td>
        <!-- <td align="right"><img src="/index/images/jian-new.png" /></td> -->
        <input type="hidden" value="{{$address['id']}}" id="address">
        <td><h3>收货地址：</h3><span>{{$address['province']}}{{$address['city']}}{{$address['area']}}{{$address['detail']}}</span></td>
       </tr>
       <tr><td colspan="3" style="height:10px; background:#efefef;padding:0;"></td></tr>
       <tr>
        <td class="dingimg" width="75%" colspan="2">选择收货时间</td>
        <td align="right"><img src="/index/images/jian-new.png" /></td>
       </tr>
       <tr><td colspan="3" style="height:10px; background:#efefef;padding:0;"></td></tr>
       <tr>
        <td>支付方式</td>
        <td align="right">
            <input type="radio" name="way" value="1" checked class="pay">支付宝支付
            <input type="radio" name="way" value="2" class="pay">微信支付
            <input type="radio" name="way" value="3" class="pay">银行卡支付
        </td>
       </tr>
       <tr><td colspan="3" style="height:10px; background:#efefef;padding:0;"></td></tr>
       <tr>
        <td class="dingimg" width="75%" colspan="2">优惠券</td>
        <td align="right"><span class="hui">无</span></td>
       </tr>
       <tr><td colspan="3" style="height:10px; background:#efefef;padding:0;"></td></tr>
       <tr>
        <td class="dingimg" width="75%" colspan="2">是否需要开发票</td>
        <td align="right"><a href="javascript:;" class="orange">是</a> &nbsp; <a href="javascript:;">否</a></td>
       </tr>
       <tr>
        <td class="dingimg" width="75%" colspan="2">发票抬头</td>
        <td align="right"><span class="hui">个人</span></td>
       </tr>
       <tr>
        <td class="dingimg" width="75%" colspan="2">发票内容</td>
        <td align="right"><a href="javascript:;" class="hui">请选择发票内容</a></td>
       </tr>
       <tr><td colspan="3" style="height:10px; background:#fff;padding:0;"></td></tr>
       <tr>
        <td class="dingimg" width="75%" colspan="3">商品清单</td>
        <input type="hidden" value="{{$goodsid}}" id="goods">
       </tr>
       @foreach($data as $k=>$v)
       <tr>
        <td class="dingimg" width="15%"><img src="http://goods.img.com/{{$v['goods_img']}}" /></td>
        <td width="50%">
         <h3>{{$v['goods_name']}}</h3>
         <time>下单时间：{{$v['created_at']}}</time>
        </td>
        <td align="right"><span class="qingdan">X {{$v['buy_num']}}</span></td>
       </tr>
       <tr>
        <th colspan="3"><strong class="orange">¥{{$v['buy_num']*$v['goods_price']}}</strong></th>
       </tr>
       @endforeach
       <tr>
        <td class="dingimg" width="75%" colspan="2">商品金额</td>
        <td align="right"><strong class="orange">¥{{$totalPrice}}</strong></td>
       </tr>
       <tr>
        <td class="dingimg" width="75%" colspan="2">折扣优惠</td>
        <td align="right"><strong class="green">¥0.00</strong></td>
       </tr>
       <tr>
        <td class="dingimg" width="75%" colspan="2">抵扣金额</td>
        <td align="right"><strong class="green">¥0.00</strong></td>
       </tr>
       <tr>
        <td class="dingimg" width="75%" colspan="2">运费</td>
        <td align="right"><strong class="orange">¥0.00</strong></td>
       </tr>
      </table>
     </div><!--dingdanlist/-->
     
     
    </div><!--content/-->
    
    <div class="height1"></div>
    <div class="gwcpiao">
     <table>
      <tr>
       <th width="10%"><a href="javascript:history.back(-1)"><span class="glyphicon glyphicon-menu-left"></span></a></th>
       <td width="50%">总计：<strong class="orange" id="price">￥{{$totalPrice}}</strong></td>
       <td width="40%"><a href="/cart/success" class="jiesuan" id="submitOrder">提交订单</a></td>
      </tr>
     </table>
    </div><!--gwcpiao/-->
    </div><!--maincont-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/index/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/index/js/bootstrap.min.js"></script>
    <script src="/index/js/style.js"></script>
    <!--jq加减-->
    <script src="/index/js/jquery.spinner.js"></script>
   <script>
	$('.spinnerExample').spinner({});
	</script>
  </body>
</html>
<script>
    $(function(){
      var pay=1;
      $('.pay').click(function(){
        $(this).prop('checked',true);
        $(this).siblings().prop('checked',false);
        pay=$(this).val();
      })
      layui.use(['layer'],function(){
        var layer=layui.layer;
        $('#submitOrder').click(function(){
          var address_id=$('#address').val();
          var goods_id=$('#goods').val();
          var price=$("strong[id='price']").text();
          $.get(
            "/cart/submitOrder",
            {address_id:address_id,goods_id:goods_id,pay:pay,price:price},
            function(res){
              if(res.code==1){
                layer.msg(res.font,{icon:res.code,time:2000},function(){
                  location.href="/cart/success?order_id="+res.order_id;
                });
              }             
            },
            'json'
          )
          return false;
        })
      })        
    })
</script>