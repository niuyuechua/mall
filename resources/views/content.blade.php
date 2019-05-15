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
    <div class="click_img">
        <img src="/{{ $value }}" width="120" height="120">
    </div>
    <div class="bg_div" style="display:none;background:#ccc;width:100%;height:100%;position:absolute;top:0;left:0;opacity:0.8;text-align:center;padding-top:5%">
        <div class="close_div" style="padding-left:30%">
                <b>关闭×</b>
        </div>
        <img src="">
    </div>
</body>
</html>
<script type="text/javascript" src="/js/jquery/jquery-3.1.1.min.js"></script>
<script type="text/javascript">
    //点击二维码
    $(".click_img").on('click',function(){
        //弹出背景层
        $(".bg_div").show();
        //获取当前点击的img标签 路径
        var src=$(this).children('img').attr('src');
        $(".bg_div").children('img').attr('src',src);
    })
    //点击关闭按钮
    $(".close_div").on('click',function(){
        //背景层隐藏
        $(".bg_div").hide();
    })
</script>