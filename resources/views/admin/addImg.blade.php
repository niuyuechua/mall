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
        <tr>
            <td><input type="file" value="添加素材" name="file"></td>
        </tr>
        <tr>
            <td><input type="submit"></td>
        </tr>
        {{csrf_field()}}
    </form>
</body>
</html>