<?php

namespace App\Admin\Controllers;

use App\ReplyModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class ReplyController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ReplyModel);

//        $grid->id('Id');
//        $grid->keyword('Keyword');
//        $grid->media_id('Media id');
//
//        return $grid;
        return view('admin.reply.addReply');
    }
    public function doAdd(Request $request){
        $fileInfo=$request->file('file');
        $keyword=$request->keyword;
        $ext=$fileInfo->getClientOriginalExtension();
        $new_filename=date('ymd').'_'.Str::random(10).'.'.$ext;
        $save_path='upload';
        $res=$fileInfo->storeAs($save_path,$new_filename);
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.getAccessToken().'&type=image';
        $client = new Client();
        $response = $client->request('post',$url,[
            'multipart' => [
                [
                    'name' => 'filename',
                    'contents' => fopen($res,'r'),
                ]
            ]
        ]);
        $json =  $response->getBody();
        $arr=json_decode($json,true);
        $data=[
            'keyword'=>$keyword,
            'media_id'=>$arr['media_id']
        ];
        $res2=ReplyModel::insert($data);
        if($res2){
            echo '添加成功';
        }else{
            echo '添加失败';
        }
    }
    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ReplyModel::findOrFail($id));

        $show->id('Id');
        $show->keyword('Keyword');
        $show->media_id('Media id');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ReplyModel);

        $form->text('keyword', 'Keyword');
        $form->text('media_id', 'Media id');

        return $form;
    }
}
