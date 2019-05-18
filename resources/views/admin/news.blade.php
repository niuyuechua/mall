<!DOCTYPE html>
<html>
<head>
    <title>群发消息</title>
</head>
<body>
    <div id="openid">
        <table border="1">
            <tr>
                <td><input type="checkbox" id="c"></td>
                <td width="50" align="center"> ID</td>
                <td width="50" align="center"> 昵称</td>
                <td width="250" align="center">openid</td>
            </tr>
            @foreach($user as $k=>$v)
                <tr>
                    <td openid="{{$v->openid}}"><input type="checkbox" class="d"></td>
                    <td width="50" align="center"> {{$v->id}}</td>
                    <td width="50" align="center"> {{$v->nickname}}</td>
                    <td width="250" align="center">{{$v->openid}}</td>
                </tr>
            @endforeach
        </table>
    </div> <br>
    <div id="tag" style="display:none">
        <table border="1">
            <tr>
                <td></td>
                <td width="50" align="center"> ID</td>
                <td width="50" align="center"> 标签名称</td>
                <td width="250" align="center">微信标签标识</td>
            </tr>
            @foreach($tag as $k=>$v)
                <tr>
                    <td tag_id="{{$v['tag_id']}}"><input type="radio" name="type"></td>
                    <td width="50" align="center"> {{$v['t_id']}}</td>
                    <td width="50" align="center"> {{$v['tag_name']}}</td>
                    <td width="250" align="center">{{$v['tag_id']}}</td>
                </tr>
            @endforeach
        </table>
    </div> <br>
    <div class="form-group">
        <label for="exampleFormControlSelect1">发送方式</label>
        <select class="form-control" id="exampleFormControlSelect1" name="type">
            <option value="0">请选择发送方式</option>
            <option value="1">根据OpenID列表群发</option>
            <option value="2">根据标签进行群发</option>
        </select>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">请输入要发送的内容:</label>
        <input type="text" id="text">
    </div>
    <button class="btn btn-primary" id="btn">发送</button>
</body>
</html>
<script type="text/javascript" src="/js/jquery/jquery-3.2.1.min.js"></script>
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
    //选择发送方式
    $(".form-control").change(function(){
        var type=$(this).val();
        if(type ==1){
            $("#openid").show();
            $("#tag").hide();
            //点击发送
            $('#btn').click(function(){
                var opid=$('.d');
                var text=$('#text').val();
                var openid='';
                opid.each(function(){
                    if($(this).prop('checked')==true) {
                        openid += $(this).parent('td').attr('openid') + ',';
                    }
                })
                openid=openid.substr(0,openid.length-1);
                //console.log(openid);
                if(openid==''){
                    alert('请选择要发送的用户');
                    return false;
                }
                if(text==''){
                    alert('请输入发送的内容');
                    return false;
                }
                $.ajax({
                    url : '/admin/sendMessage',
                    data:{openid,text},
                    type:'get',
                    dataType:'json',
                    success:function(res){
                        if(res=='1'){
                            alert("群发成功");
                        }else{
                            alert("群发失败");
                        }
                    }
                })
            })
        }else if(type ==2){
            $("#openid").hide();
            $("#tag").show();
            //点击发送
            $('#btn').click(function(){
                var tag_id=$('input:radio:checked').parent('td').attr('tag_id');
                //console.log(tag_id);
                var text=$('#text').val();
                if(tag_id=='undefined'){
                    alert('请选择用户标签');
                    return false;
                }
                if(text==''){
                    alert('请输入发送内容');
                    return false;
                }
                $.ajax({
                    url : '/admin/test',
                    data:{tag_id,text},
                    type:'get',
                    dataType:'json',
                    success:function(res){
                        if(res=='1'){
                            alert("群发成功");
                        }else{
                            alert("群发失败");
                        }
                    }
                })
            })
        }else if(type==0){
            alert('请选择发送方式');
            return false;
        }
    })
</script>