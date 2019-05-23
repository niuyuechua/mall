<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <div style="text-align:center;padding-top:10%;">
        <h2>微信扫描下方二维码</h2>
        <h3 class="hint" style="color:red"></h3>
        <img src="http://qr.liantu.com/api.php?bg=f3f3f3&fg=ff0000&gc=222222&el=l&w=300&m=10&text={{$text}}"/> <br>
        <a href="/login">账号登录</a>
    </div>
</body>
</html>
<script type="text/javascript" src="/js/jquery/jquery-3.2.1.min.js"></script>
<script>
    var lunxun =setInterval("findOpenid()",3000);
    function findOpenid(){
        var id="{{$random}}";
        $.ajax({
            url:'/login/checkScan',
            data:{id:id},
            type:'get',
            dataType:'json',
            success:function(res){
                $(".hint").text(res.msg);
                clearInterval(lunxun);
                if(res.code==1){
                    header('refresh:3,url=/admin');
                }
            }
        })
    }
</script>