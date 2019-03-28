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
       <h1>收货地址</h1>
      </div>
     </header>
     <div class="head-top">
      <img src="/index/images/head.jpg" />
     </div><!--head-top/-->
     <form action="/user/addressAdd" method="post" class="reg-login">
        @csrf
      <div class="lrBox">
       <div class="lrList"><input type="text" placeholder="收货人" id="name"/></div>
       <div class="lrList"><input type="text" placeholder="详细地址" id="detail"/></div>
       <div class="lrList">
        <select class="address" id="pro">
         <option value=''>省份/直辖市</option>
         @foreach($province as $k=>$v)
         <option value="{{$v->id}}">{{$v->name}}</option>
         @endforeach
        </select>
       </div>
       <div class="lrList">
        <select class="address" id="city">
         <option value=''>市区</option>
        </select>  
       </div>
       <div class="lrList">
        <select class="address" id="area">
         <option value=''>区县</option>
        </select>
       </div>
       <div class="lrList"><input type="text" placeholder="手机号" id="tel"/></div>
       <div><input type="checkbox" id="moren">设为默认</div>
      </div><!--lrBox/-->
      <div class="lrSub">
       <input type="submit" value="保存" id="add"/> 
      </div>
     </form><!--reg-login/-->
     
     <div class="height1"></div>
     <div class="footNav">
      <dl>
       <a href="index.html">
        <dt><span class="glyphicon glyphicon-home"></span></dt>
        <dd>微店</dd>
       </a>
      </dl>
      <dl>
       <a href="prolist.html">
        <dt><span class="glyphicon glyphicon-th"></span></dt>
        <dd>所有商品</dd>
       </a>
      </dl>
      <dl>
       <a href="car.html">
        <dt><span class="glyphicon glyphicon-shopping-cart"></span></dt>
        <dd>购物车 </dd>
       </a>
      </dl>
      <dl class="ftnavCur">
       <a href="user.html">
        <dt><span class="glyphicon glyphicon-user"></span></dt>
        <dd>我的</dd>
       </a>
      </dl>
      <div class="clearfix"></div>
     </div><!--footNav/-->
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
        layui.use(['layer'],function(){
            var layer=layui.layer;
            var _token=$("input[name='_token']").val();
            //console.log(_token);
            //三级联动
            $(".address").change(function(){
                var id=$(this).val();
                //console.log(id);
                var _option="<option value='' selected>--请选择--</option>";
                var _this=$(this);
                //         >!!!>>> Ajax中不能使用$(this) <<<!!!<
                $.get(
                    "/user/areaInfo",
                    {id:id},
                    function(res){
                      if(res.code==1){
                          for(var i in res['areaInfo']){
                              _option+="<option value='"+res['areaInfo'][i]['id']+"'>"+res['areaInfo'][i]['name']+"</option>";
                          }
                          console.log(_option);
                          _this.parent('div').next('div').children('select').html(_option);
                      }else{
                          layer.msg(res.font,{icon:res.code})
                      }
                    },
                    'json'
                )
            })
            //保存收货地址
            $("#add").click(function(){
                var name=$("#name").val();
                var detail=$('#detail').val();
                var pro=$('#pro').val();
                var city=$('#city').val();
                var area=$('#area').val();
                var tel=$('#tel').val();
                var moren=$('#moren').prop('checked');
                //console.log(moren);
                $.post(
                    "/user/addressAdd",
                    {user_name:name,detail:detail,province:pro,city:city,area:area,tel:tel,default:moren,_token:_token},
                    function(res){
                        if(res.code==1){
                            layer.msg(res.font,{icon:res.code,time:2000},function(){
                                location.href="/user/addressEdit";
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