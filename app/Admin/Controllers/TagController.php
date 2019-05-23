<?php

namespace App\Admin\Controllers;

use App\TagModel;
use App\Http\Controllers\Controller;
use App\TmpUserModel;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;
use Tests\Models\Tag;

class TagController extends Controller
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
//        $grid = new Grid(new TagModel);
//
//        $grid->id('Id');
//        $grid->tag_name('Tag name');
//        $grid->tag_id('Tag id');
//
//        return $grid;
        return view('admin.tag.addTag');
    }

    public function addTag(){
        $tag_name=request()->tag_name;
        $url="https://api.weixin.qq.com/cgi-bin/tags/create?access_token=".getAccessToken();
        $post_data=[
            'tag'=>[
                'name'=>$tag_name
            ]
        ];
        $json_data=json_encode($post_data,JSON_UNESCAPED_UNICODE);
        $client=new Client();
        $res=$client->request('POST',$url,[
            'body'=>$json_data
        ]);
        $json_res=$res->getBody();
        $arr_res=json_decode($json_res,true);
        //dump($arr_res);
        $tag_id=$arr_res['tag']['id'];
        $arr=[
            'tag_name'=>$tag_name,
            'tag_id'=>$tag_id
        ];
        $res=TagModel::insert($arr);
        if($res){
            echo '标签添加成功';
            header("Refresh:2;url=/admin/taglist");
        }else{
            echo '标签添加失败';
            header("Refresh:2;url=/admin/tag");
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
        $show = new Show(TagModel::findOrFail($id));

        $show->id('Id');
        $show->tag_name('Tag name');
        $show->tag_id('Tag id');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new TagModel);

        $form->text('tag_name', 'Tag name');
        $form->number('tag_id', 'Tag id');

        return $form;
    }
}
