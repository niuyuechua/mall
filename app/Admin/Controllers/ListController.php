<?php

namespace App\Admin\Controllers;

use App\MaterialModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ListController extends Controller
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
        $grid = new Grid(new MaterialModel);
        $server_name=$_SERVER['SERVER_NAME'];
        //删除media_id过期的图片（media_id有效期3天）
        $data=MaterialModel::get();
        foreach($data as $k=>$v){
            if(time()-$v['created_at']>3*24*3600){
                MaterialModel::where('id',$v['id'])->delete();
            }
        }

        $grid->id('Id');
        $grid->media_name('媒体文件名称');
        $grid->media_id('Media id');
        $grid->url('Url')->image('http://'.$server_name);    //lavarel后台根据此域名对应的文件夹去找图片
        $grid->type('媒体文件类型');
        $grid->material_type('素材类型')->display(function($material_type){
            if($material_type==1){
                return "临时素材";
            }else{
                return "永久素材";
            }
        });
        $grid->created_at('上传时间')->display(function($created_at){
            return date('Y-m-d H:i',$created_at);
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(MaterialModel::findOrFail($id));

        $show->id('Id');
        $show->type('Type');
        $show->media_id('Media id');
        $show->img_url('Img url');
        $show->created_at('Created at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MaterialModel);

        $form->text('type', 'Type');
        $form->text('media_id', 'Media id');
        //$form->text('img_url', 'Img url');
        $form->image('img_url', 'Img url');

        return $form;
    }
}
