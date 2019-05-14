<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>素材添加</title>
</head>
<body>
    <form action="/admin/material/addImg" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="exampleInputEmail1">菜单名称</label>
            <input type="text" name="media_name">
        </div>

        <div class="form-group">
            <label for="exampleFormControlSelect1">素材类型</label>
            <select class="form-control" id="exampleFormControlSelect1" name="material_type">
                <option value="1">临时素材</option>
                <option value="2">永久素材</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">请选择文件</label>
            <input type="file" value="添加素材" name="file">
        </div>
        <div class="form-group">
            <label for="exampleFormControlSelect1">媒体文件类型</label>
            <select class="form-control" id="exampleFormControlSelect1" name="type">
                <option value="image">image</option>
                <option value="voice">voice</option>
                <option value="video">video</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
        {{csrf_field()}}
    </form>

</body>
</html>