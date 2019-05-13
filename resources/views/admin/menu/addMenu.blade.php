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
    <form action="/admin/menu/addMenu" method="post">
        <div class="form-group">
            <label for="exampleFormControlSelect1">父级菜单</label>
            <select class="form-control" id="exampleFormControlSelect1" name="parent_id">
                <option value="0">无父级</option>
                @foreach($info as $v)
                <option value="{{$v['id']}}">{{$v['menu_name']}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">菜单名称</label>
            <input type="text" name="menu_name">
        </div>
        <div class="form-group">
            <label for="exampleFormControlSelect1">菜单类型</label>
            <select class="form-control" id="exampleFormControlSelect1" name="menu_type">
                <option value="view">view</option>
                <option value="click">click</option>
                <option value="location_select">location_select</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">菜单标识</label>
            <input type="text" name="menu_key" placeholder="请输入click菜单的标识key或view菜单的url">
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
        {{csrf_field()}}
    </form>
</body>
</html>