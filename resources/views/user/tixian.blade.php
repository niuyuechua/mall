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
       <h1>银行卡</h1>
      </div>
     </header>
     <div class="head-top">
      <img src="/index/images/head.jpg" />
     </div><!--head-top/-->
     <div class="addYinhang">
     <h3>添加银行卡 <span class="glyphicon glyphicon-remove"></span></h3>
     <select>
      <option>工商银行</option>
      <option>中国银行</option>
      <option>农业银行</option>
      <option>邮政银行</option>
     </select>
     <input type="text" placeholder="输入银行卡号" />
     <div class="yhts">
      注意：此银行卡开户人必须和您实名认证的名字一样，否则提现无法成功到账
     </div><!--yhts/-->
     <div class="moren">
      <input type="checkbox" /> 设为默认银行
     </div><!--moren/-->
     <a href="#" class="addTian">添加</a>
    </div><!--addYinhang/-->
    
    <div class="jyjl">
         <h2 class="vipTitle">
          <span>银行卡绑定</span>
         </h2>
         <div class="yinhangka">
          <div class="yhangkaList">
           <h3>工商银行</h3>
           <div class="yinhangMeass">
            <span class="hui">账号 </span> 6222 0000 0000 0000
            
            <a class="removeyin" href="#">[删除]</a>
           </div><!--yinhangMeass-->
          </div><!--yhangkaList/-->
          
          <div class="yhangkaList">
           <div class="tianjiayinhang">
            <span class="glyphicon glyphicon-plus"></span><br />
            <span class="blue">添加一张银行卡</span>
           </div><!--tianjiayinhang/-->
          </div><!--yhangkaList/-->
          
          <div class="clearfix"></div>
         </div><!--yinhangka/-->
        </div><!--jyjl/--> 
        <div class="jyjl">
         <div class="chongzhi">
          <h3>填写提现金额</h3>
          <form action="vip.html" method="get" class="form-login-reg">
              <div class="flrLeft">
               <dl>
                <dt>账户余额：</dt>
                <dd>
                 <strong class="red" style="font-size:22px;">0.00</strong> <strong>元</strong>
                </dd>
                <div class="clearfix"></div>
               </dl>
               <dl>
                <dt>提现金额：</dt>
                <dd>
                 <input type="text" class="flrwidht1" /> <strong>元</strong> <span>必填！</span>
                </dd>
                <div class="clearfix"></div>
               </dl>
               <dl>
                <dt>验证码：</dt>
                <dd>
                 <input type="text" class="flrwidht2" /> <span>必填！</span> 
                 <img src="/index/images/yzm.gif" width="77" height="33" />
                </dd>
                <div class="clearfix"></div>
               </dl>
               <div class="gongyue">
                <input type="checkbox" /> 本人已阅读,并已确认一下重要信息
               </div><!--gongyue/-->
               <div class="flrSub flrSub3">
                <input type="submit" value=" 确认提现 " />
               </div><!--flrSub/-->
              </div><!--flrLeft/-->
              <div class="clearfix"></div>
            </form><!--form-login-reg/-->
         </div><!--chongzhi/-->
         <div class="czts">
          <span>温馨提示</span><br />
          1.提款申请提交后，您的提现金额将从可用金额中扣除，无法在进行出借。<br />
          2.为防止信用卡套现、洗钱等违法行为，网站将针对异常提款(包括无消费充值资金)进行严格审核，审核时间在15个工作日之后。<br />
          3.提现银行账号的开户名必须与银行卡名一致，否则提现失败。
         </div><!--czts/-->
        </div><!--jyjl/-->
     
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