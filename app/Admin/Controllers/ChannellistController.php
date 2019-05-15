<?php

namespace App\Admin\Controllers;

use App\ChannelModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class ChannellistController extends Controller
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
        $grid = new Grid(new ChannelModel);
        $server_name=$_SERVER['SERVER_NAME'];

        $grid->id('Id');
        $grid->channel_name('渠道名称');
        $grid->channel_sign('渠道标识');
        $grid->num('关注人数');
        $grid->qrcode_url('渠道二维码')->image('http://'.$server_name,120,120)->modal('最新评论', function ($qrcode_url) {
            return new Table(['ID'],"<img src='/.$qrcode_url.'>");
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
