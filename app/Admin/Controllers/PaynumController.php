<?php

namespace App\Admin\Controllers;

use App\ChannelModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class PaynumController extends Controller
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
//        $grid = new Grid(new ChannelModel);
//
//        $grid->id('Id');
//        $grid->channel_name('Channel name');
//        $grid->channel_sign('Channel sign');
//        $grid->num('Num');
//        $grid->qrcode_url('Qrcode url');
//
//        return $grid;
        $data=ChannelModel::get();
        $name='';
        $num='';
        foreach($data as $k=>$v){
            $name.="'".$v['channel_name']."',";
            $num.=$v['num'].",";
        }
        $name=trim($name,",");
        $num=trim($num,",");
        $arr=[
            'name'=>$name,
            'num'=>$num
        ];
        return view('admin.channel.payNum',$arr);
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ChannelModel::findOrFail($id));

        $show->id('Id');
        $show->channel_name('Channel name');
        $show->channel_sign('Channel sign');
        $show->num('Num');
        $show->qrcode_url('Qrcode url');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ChannelModel);

        $form->text('channel_name', 'Channel name');
        $form->text('channel_sign', 'Channel sign');
        $form->number('num', 'Num');
        $form->text('qrcode_url', 'Qrcode url');

        return $form;
    }
}
