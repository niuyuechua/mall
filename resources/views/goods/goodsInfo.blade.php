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
       <h1>产品详情</h1>
      </div>
     </header>
     <div id="sliderA" class="slider">
     @foreach($data['goods_imgs'] as $k=>$v)
      <img src="http://goods.img.com/{{$v}}" />
      @endforeach
     </div><!--sliderA/-->
     <table class="jia-len">
       <input type="hidden" class="id" value="{{$data['goods_id']}}">
       <input type="hidden" class="num" value="{{$data['goods_num']}}">
      <tr>
       <th><strong class="orange">{{$data['goods_price']}}</strong></th>
       <td>
        <input type="text" class="spinnerExample" />
       </td>
      </tr>
      <tr>
       <td>
        <strong>{{$data['goods_name']}}</strong>
        <p class="hui">富含纤维素，平衡每日膳食</p>
       </td>
       <td align="right">
        <a href="javascript:;" class="shoucang"><span class="glyphicon glyphicon-star-empty"></span></a>
       </td>
      </tr>
     </table>
     <div class="height2"></div>
     <h3 class="proTitle">商品规格</h3> 
     <ul class="guige">
      <li class="guigeCur"><a href="javascript:;">50ML</a></li>
      <li><a href="javascript:;">100ML</a></li>
      <li><a href="javascript:;">150ML</a></li>
      <li><a href="javascript:;">200ML</a></li>
      <li><a href="javascript:;">300ML</a></li>
      <div class="clearfix"></div>
     </ul><!--guige/-->
     <div class="height2"></div>
     <div class="zhaieq">
      <a href="javascript:;" class="zhaiCur">商品简介</a>
      <a href="javascript:;">商品参数</a>
      <a href="javascript:;" style="background:none;">订购列表</a>
      <div class="clearfix"></div>
     </div><!--zhaieq/-->
     <div class="proinfoList">
      <img src="http://goods.img.com/{{$data['goods_img']}}" width="636" height="822" />
      {{$data['goods_desc']}}
     </div><!--proinfoList/-->
     <div class="proinfoList">
      暂无信息....
     </div><!--proinfoList/-->
     <div class="proinfoList">
      暂无信息......
     </div><!--proinfoList/-->
     <table class="jrgwc">
      <tr>
       <th>
        <a href="index.html"><span class="glyphicon glyphicon-home"></span></a>
       </th>
       <td><a href="javascript:;" id="cartAdd">加入购物车</a>{{@csrf_field()}}</td>
      </tr>
     </table>
    </div><!--maincont-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/index/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/index/js/bootstrap.min.js"></script>
    <script src="/index/js/style.js"></script>
    <!--焦点轮换-->
    <script src="/index/js/jquery.excoloSlider.js"></script>
    <script>
		$(function () {
		 $("#sliderA").excoloSlider();
		});
	</script>
     <!--jq加减-->
    <script src="/index/js/jquery.spinner.js"></script>
   <script>
	$('.spinnerExample').spinner({});
	</script>
  </body>
</html>
<script>
    $(function(){
        //购买数量默认为一
        $('.value').each(function(){
          $(this).val(1);
        })
         layui.use(['form'],function(){
            var form=layui.form;
            var goods_num=parseInt($('.num').val());
            // 点击+号
            $('.increase').click(function(){
                var buy_num=parseInt($('.spinnerExample').val());
                //console.log(buy_num);
                if(buy_num>=goods_num){
                    $(this).prop('disabled',true)              
                }else{
                    //buy_num=buy_num+1
                    $('.spinnerExample').val(buy_num)
                    $(this).siblings('button').prop('disabled',false)                    
                }
            })
            // 点击-号
            $('.decrease').click(function(){
                var buy_num=parseInt($('.spinnerExample').val());
                if(buy_num<=1){
                    $(this).prop('disabled',true)
                }else{
                    //buy_num=buy_num-1
                    $('.spinnerExample').val(buy_num);
                    $(this).siblings('button').prop('disabled',false)                    
                }
            })
            // 失去焦点
            $('.spinnerExample').blur(function(){
                var buy_num=parseInt($('.spinnerExample').val());    
                var reg=/^\d+$/;
                if(!reg.test(buy_num)){
                    $('.spinnerExample').val(1)
                }else if(buy_num>=goods_num){
                    $('.spinnerExample').val(goods_num)     
                }else if(buy_num<=1){
                    $('.spinnerExample').val(1) 
                }
            })
            $('#cartAdd').click(function(){
                var goods_id=$('.id').val()
                var buy_num=parseInt($('.spinnerExample').val());
                var _token=$(this).next().val();
                $.post(
                    "/goods/cartAdd",
                    {goods_id:goods_id,buy_num:buy_num,_token:_token},
                    function(res){
                        layer.msg(res.font,{icon:res.code});				          
                    },
                    'json'
                )
            })
         })
    })
</script>