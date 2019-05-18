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
    <input type="hidden" value="{{$t_id}}" id="t_id">
    <table border="1">
        <tr>
            <td><input type="checkbox" id="c"></td>
            <td width="50" align="center"> ID</td>
            <td width="50" align="center"> 昵称</td>
            <td width="250" align="center">openid</td>
        </tr>
        @foreach($data as $k=>$v)
            <tr>
                <td openid="{{$v['openid']}}">
                    @if(in_array($v['openid'],$openid))
                        <input type="checkbox" class="d" disabled>
                    @else
                        <input type="checkbox" class="d">
                    @endif
                </td>
                <td width="50" align="center"> {{$v['id']}}</td>
                <td width="50" align="center"> {{$v['nickname']}}</td>
                <td width="250" align="center">{{$v['openid']}}</td>
            </tr>
        @endforeach
    </table> <br>
    <button class="btn btn-primary" id="btn">打标签</button>
</body>
</html>
<script type="text/javascript" src="/js/jquery/jquery-1.12.4.min.js"></script>
<script>
    //点击全选
    $('#c').click(function(){
        var type=$('#c').prop('checked');
        $('.d').prop('checked',type);
    })
    //取消选择某个复选框
    $('.d').click(function(){
        if($(this).prop('checked')==false){
            $('#c').prop('checked',false);
        }
    })
    //点击打标签
    $('#btn').click(function(){
        var opid=$('.d');
        var t_id=$('#t_id').val();
        var openid='';
        opid.each(function(res){
            if($(this).prop('checked')==true) {
                openid += '"'+$(this).parent('td').attr('openid') + '",';
            }
        })
        openid=openid.substr(0,openid.length-1);
        if(openid==''){
            alert('请选择要分配的用户');
            return false;
        }
        $.ajax({
            url : '/admin/makeTag',
            data:{openid,t_id},
            type:'get',
            dataType:'json',
            success:function(res){
                if(res==1){
                    alert("分配成功");
                }else{
                    alert("分配失败");
                }
            }
        })
    })
</script>