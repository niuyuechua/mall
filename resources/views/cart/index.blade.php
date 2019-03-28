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
    @if (session('message'))
    <script>
    layui.use(['layer'],function(){
      var layer=layui.layer;
      layer.msg("{{ session('message') }}",{icon:2});
    })   
    </script>
    @endif

    {{@csrf_field()}}
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
     <table class="shoucangtab">
      <tr>
       <td width="75%"><span class="hui">购物车共有：<strong class="orange">{{$count}}</strong>件商品</span></td>
       <td width="25%" align="center" style="background:#fff url(/index/images/xian.jpg) left center no-repeat;">
        <span class="glyphicon glyphicon-shopping-cart" style="font-size:2rem;color:#666;"></span>
       </td>
      </tr>
     </table>
     
     <div class="dingdanlist">
      <table>
       <tr>
        <td width="100%" colspan="4"><a href="javascript:;"><input type="checkbox" name="1" id="all"/> 全选</a></td>
       </tr>
       @foreach($data as $k=>$v)
       <tr goods_id="{{$v->goods_id}}" cart_id="{{$v->cart_id}}">
        <td width="4%"><input type="checkbox" name="1" class="one"/></td>
        <td class="dingimg" width="15%"><img src="http://goods.img.com/{{$v->goods_img}}" /></td>
        <td width="50%">
         <h3>{{$v->goods_name}}</h3>
         <time>添加时间：{{$v->created_at}}</time>
        </td>
        <td align="right">
          <input type="text" class="spinnerExample" buy_num="{{$v->buy_num}}" goods_num="{{$v->goods_num}}"/>
        </td>
       </tr>
       <tr>
        <th colspan="4"><strong class="orange">¥{{$v->goods_price*$v->buy_num}}</strong></th>
       </tr>
       @endforeach
      </table>
     </div><!--dingdanlist/-->
     
     <div class="height1"></div>
     <div class="gwcpiao">
     <table>
      <tr>
       <th width="10%"><a href="javascript:history.back(-1)"><span class="glyphicon glyphicon-menu-left"></span></a></th>
       <td width="50%">总计：<strong class="orange" id="totalPrice">¥0.00</strong></td>
       <td width="40%"><a href="javascript:;" class="jiesuan" id="confirmCount">去结算</a></td>
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
      //购买数量默认值
      $('.value').each(function(){
          var buy_num=$(this).attr('buy_num');
          //console.log(buy_num);
          $(this).val(buy_num);
      })
      //模板减号默认不能点，去除该样式
      $('.decrease').each(function(){
          var buy_num=$(this).next().val();
          if(buy_num>1){
            $(this).removeAttr('disabled');
          }
      })

      layui.use('layer',function(){
      var layer = layui.layer;
      var _token=$("input[name='_token']").val();
      //点击加号
      $('.increase').click(function(){
        var _this=$(this);
        var goods_id=_this.parents('tr').attr('goods_id');
        var cart_id=_this.parents('tr').attr('cart_id');
        // alert(goods_id);
        var val=parseInt(_this.prev().val());
        var num=_this.prev().attr('goods_num');
        //console.log(val);
        if(val>=num){
          _this.prev().val(num);
          _this.attr('disabled','true');
        }else{
          _this.prev().val(val);
          $(".decrease").removeAttr('disabled');
        }
        
        $.post(
          '/cart/checkNum',
          {_token:_token,buy_num:val,goods_id:goods_id,cart_id:cart_id},
          function(res){
              layer.msg(res.font,{icon:res.code});
              if(res.code==1){
                _this.parents('tr').next('tr').find("strong[class='orange']").text('¥'+res.price);
              }
          },
          'json'
        )
        total();
      })
      //点击减号
      $('.decrease').click(function(){
        var _this=$(this);
        var goods_id=_this.parents('tr').attr('goods_id');
        var cart_id=_this.parents('tr').attr('cart_id');
        var val=parseInt(_this.next().val());       
        if(val<=1){
          _this.next().val(1);
          _this.attr('disabled','true');
        }else{
          _this.next().val(val);
          $(".increase").removeAttr('disabled'); 
        }
        
        $.post(
          '/cart/checkNum',
          {_token:_token,buy_num:val,goods_id:goods_id,cart_id:cart_id},
          function(res){
              layer.msg(res.font,{icon:res.code});
              if(res.code==1){
                _this.parents('tr').next('tr').find("strong[class='orange']").text('¥'+res.price);
              }
          },
          'json'
        )
        total();
      })
      //输入框内容改变
      $('.value').change(function(){
        var _this=$(this);
        var goods_id=_this.parents('tr').attr('goods_id');
        var cart_id=_this.parents('tr').attr('cart_id');
        var val=parseInt(_this.val());
        var num=_this.attr('goods_num');
        var reg=/^\d{1,}$/;
        if(!reg.test(val)){
          _this.val(1);
        }else if(val<=1){
          val=1;
          _this.val(1)
        }else if(val>=num){
          _this.val(num);
        }
        //console.log(val);
        $.post(
          '/cart/checkNum',
          {_token:_token,buy_num:val,goods_id:goods_id,cart_id:cart_id},
          function(res){
              layer.msg(res.font,{icon:res.code});           
              _this.parents('tr').next('tr').find("strong[class='orange']").text('¥'+res.price);             
          },
          'json'
        )
        total();
      })
      //点击全选
      $('#all').click(function(){
        var status=$(this).prop('checked');
        $('.one').prop('checked',status);
        total();
      })
      //点击复选框
      $('.one').click(function(){
        total();
      })
      //获取总价
      function total(){
        var check=$('.one');
        var cart_id='';
        check.each(function(index){
          if($(this).prop('checked')==true){
            cart_id+=$(this).parents('tr').attr('cart_id')+',';
          }
        })
          cart_id=cart_id.substr(0,cart_id.length-1);
          //console.log(cart_id);
          $.post(
            "/cart/total",
            {_token:_token,cart_id:cart_id},
            function(res){
                $('#totalPrice').text('￥'+res);
            }
          ) 
      }
      //点击提交
      $(document).on('click','#confirmCount',function(){
          var check=$('.one');
          var goods_id='';
          check.each(function(index){
              if($(this).prop('checked')==true){
                  goods_id+=$(this).parents('tr').attr('goods_id')+',';
              }
          })
          goods_id=goods_id.substr(0,goods_id.length-1);
          //console.log(goods_id);
          location.href="/cart/pay?goods_id="+goods_id;
      })
      })  
    })
</script>