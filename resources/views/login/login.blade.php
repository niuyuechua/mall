<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style>
*{
	padding:0px;
	margin:0px;
	}

body{
	font-family:Arial, Helvetica, sans-serif;
	background:url(/login_images/login/grass.jpg);
	font-size:13px;
    
	}
img{
	border:0;
	}
.lg{width:468px; height:468px; margin:100px auto; background:url(/login_images/login/login_bg.png) no-repeat;}
.lg_top{ height:200px; width:468px;}
.lg_main{width:400px; height:180px; margin:0 25px;}
.lg_m_1{
	width:290px;
	height:100px;
	padding:60px 55px 20px 55px;
}
.ur{
	height:37px;
	border:0;
	color:#666;
	width:236px;
	margin:4px 28px;
	background:url(/login_images/login/user.png) no-repeat;
	padding-left:10px;
	font-size:16pt;
	font-family:Arial, Helvetica, sans-serif;
}
.pw{
	height:37px;
	border:0;
	color:#666;
	width:236px;
	margin:4px 28px;
	background:url(/login_images/login/password.png) no-repeat;
	padding-left:10px;
	font-size:16pt;
	font-family:Arial, Helvetica, sans-serif;
}
.bn{width:330px; height:72px; background:url(/login_images/login/enter.png) no-repeat; border:0; display:block; font-size:18px; color:#FFF; font-family:Arial, Helvetica, sans-serif; font-weight:bolder;}
.lg_foot{
	height:80px;
	width:330px;
	padding: 6px 68px 0 68px;
}
</style>
</head>

<body class="b">
<div class="lg">
<form action="/login/doLogin" method="POST">
    <div class="lg_main">
        <div class="lg_m_1">
        <input name="name" placeholder="用户名" class="ur" />
        <input name="pwd" type="password" placeholder="密码" class="pw" /> <br><br>
        <div style="padding-left:30px">
            <img src="/login_images/login/untitled.jpg" width="150">
            <input type="text" name="code" placeholder="请输入验证码"> <input type="button" value="获取验证码" onclick="sendCode(this)" class="code" />
        </div>
        </div>
    </div>
    <br><br><br><br><br><br><br><br><br><br><br><br>
    <div class="lg_foot">
    <input type="submit" value="Login In" class="bn" /></div>
    {{csrf_field()}}
</form>
</div>

</body>
</html>
<script type="text/javascript" src="/js/jquery/jquery-3.2.1.min.js"></script>
<script>
    $(".code").on('click',function(){
        //ajax请求，发送验证码
        var name=$(".ur").val();
        var pwd=$(".pw").val();

        $.ajax({
            url : '/login/sendCode',
            data:{name,pwd},
            type:'get',
            dataType:'json',
            success:function(res){
                if(res==0){
                    alert("用户名或密码错误");
                }else if(res==1){
                    alert("验证码发送成功");
                }else{
                    alert("验证码发送失败");die;
                }
            }
        })
    })

    //点击获取后倒计时
    var clock = '';
    var nums = 60;
    var btn;
    function sendCode(thisBtn)
    {
        btn = thisBtn;
        btn.disabled = true; //将按钮置为不可点击
        btn.value = nums+'秒后可重新获取';
        clock = setInterval(doLoop, 1000); //一秒执行一次
    }
    function doLoop()
    {
        nums--;
        if(nums > 0){
            btn.value = nums+'秒后可重新获取';
        }else{
            clearInterval(clock); //清除js定时器
            btn.disabled = false;
            btn.value = '获取验证码';
            nums = 10; //重置时间
        }
    }
</script>