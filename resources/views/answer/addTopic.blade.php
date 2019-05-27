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
    <form action="/wx/doAdd" method="post">
        <div class="form-group">
            <label for="exampleInputEmail1">题目</label>
            <input type="text" name="topic">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">答案A：</label>
            <input type="text" name="answer_A">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">答案B：</label>
            <input type="text" name="answer_B">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">正确答案</label>
            <input type="text" name="correct">
        </div>
        <button type="submit" class="btn btn-primary">提交</button>
        {{csrf_field()}}
    </form>
</body>
</html>