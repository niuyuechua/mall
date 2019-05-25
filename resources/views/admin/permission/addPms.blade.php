<div style="padding-left:50px;padding-top:30px;">
<form action="/admin/addPms/doAdd" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="role_id" value="1">
        <div class="form-group">
            <div class="form-group">
                <label for="exampleFormControlSelect1">角色名称</label>
                <select class="form-control" id="exampleFormControlSelect1" name="role_id">
                    @foreach($role as $k=>$v)
                    <option value="{{$v['role_id']}}">{{$v['role_name']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <h4>选择权限</h4>
        <table class="table table-bordered">
            <tbody>
            @foreach($pms as $k=>$v)
            <tr>
                <td width="18%" valign="top" class="first-cell">
                    <input type="checkbox" class="checkbox" value="{{$v[0]['parent_id']}}" name="pms_id[]">
                    {{$k}}
                </td>
                <td>
                    @foreach($v as $key=>$value)
                    <div style="width:200px;float:left;">
                        <label for="sms_send"><input type="checkbox" name="pms_id[]" value="{{$value['pms_id']}}" id="sms_send" class="checkbox" title="">
                            {{$value['pms_name']}}</label>
                    </div>
                    @endforeach
{{--                    <div style="width:200px;float:left;">--}}
{{--                        <label for="sms_send"><input type="checkbox" name="power_id[]" value="7 " id="sms_send" class="checkbox" title="" checked="">--}}
{{--                            渠道展示</label>--}}
{{--                    </div>--}}
{{--                    <div style="width:200px;float:left;">--}}
{{--                        <label for="sms_send"><input type="checkbox" name="power_id[]" value="8 " id="sms_send" class="checkbox" title="" checked="">--}}
{{--                            渠道统计</label>--}}
{{--                    </div>--}}
                </td>
            </tr>
            @endforeach
{{--            <tr>--}}
{{--                <td width="18%" valign="top" class="first-cell">--}}
{{--                    <input type="checkbox" class="checkbox" value="2" name="power_id[]" checked="">--}}
{{--                    素材管理--}}
{{--                </td>--}}
{{--                <td>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td width="18%" valign="top" class="first-cell">--}}
{{--                    <input type="checkbox" class="checkbox" value="3" name="power_id[]" checked="">--}}
{{--                    微信菜单管理--}}
{{--                </td>--}}
{{--                <td>--}}
{{--                    <div style="width:200px;float:left;">--}}
{{--                        <label for="sms_send"><input type="checkbox" name="power_id[]" value="4 " id="sms_send" class="checkbox" title="" checked="">--}}
{{--                            菜单添加</label>--}}
{{--                    </div>--}}
{{--                    <div style="width:200px;float:left;">--}}
{{--                        <label for="sms_send"><input type="checkbox" name="power_id[]" value="5 " id="sms_send" class="checkbox" title="" checked="">--}}
{{--                            菜单展示</label>--}}
{{--                    </div>--}}
{{--                    <div style="width:200px;float:left;">--}}
{{--                        <label for="sms_send"><input type="checkbox" name="power_id[]" value="9 " id="sms_send" class="checkbox" title="" checked="">--}}
{{--                            菜单添加-jq版</label>--}}
{{--                    </div>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td width="18%" valign="top" class="first-cell">--}}
{{--                    <input type="checkbox" class="checkbox" value="11" name="power_id[]" checked="">--}}
{{--                    权限管理--}}
{{--                </td>--}}
{{--                <td>--}}
{{--                    <div style="width:200px;float:left;">--}}
{{--                        <label for="sms_send"><input type="checkbox" name="power_id[]" value="12 " id="sms_send" class="checkbox" title="" checked="">--}}
{{--                            添加权限</label>--}}
{{--                    </div>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--            <tr>--}}
{{--                <td width="18%" valign="top" class="first-cell">--}}
{{--                    <input type="checkbox" class="checkbox" value="13" name="power_id[]" checked="">--}}
{{--                    角色管理--}}
{{--                </td>--}}
{{--                <td>--}}
{{--                    <div style="width:200px;float:left;">--}}
{{--                        <label for="sms_send"><input type="checkbox" name="power_id[]" value="14 " id="sms_send" class="checkbox" title="" checked="">--}}
{{--                            角色展示</label>--}}
{{--                    </div>--}}
{{--                </td>--}}
{{--            </tr>--}}

            </tbody>
        </table>
        <button type="submit" class="btn btn-default">确定</button>
    {{csrf_field()}}
</form>
</div>