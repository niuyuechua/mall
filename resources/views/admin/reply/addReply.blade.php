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
    <form action="/admin/addReply/doAdd" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="exampleInputEmail1">输入关键字</label>
            <input type="text" name="keyword">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">请选择文件</label>
            <input type="file" value="添加素材" name="file">
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
        {{csrf_field()}}
    </form>
</body>
</html>