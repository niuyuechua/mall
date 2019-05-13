<?php

namespace App\Admin\Controllers;

use App\MenuModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class MenuController extends Controller
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
//        $grid = new Grid(new MenuModel);
//
//        $grid->id('Id');
//        $grid->menu_name('Menu name');
//        $grid->menu_type('Menu type');
//        $grid->menu_key('Menu key');
//
//        return $grid;
        return view('admin.menu.addMenu');
    }
    public function addMenu(Request $request){
        $data=$request->input();
        //dump($data);
        $data=[
            'menu_name'=>$data['menu_name'],
            'menu_type'=>$data['menu_type'],
            'menu_key'=>$data['menu_key'],
        ];
        $info=MenuModel::get();
        $num=count($info,1);
        if($num>=3){
            echo "菜单不能超过3个";die;
        }
        $res=MenuModel::insert($data);
        if($res){
            echo "菜单添加成功";
        }else{
            echo "菜单添加失败";
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
        $show = new Show(MenuModel::findOrFail($id));

        $show->id('Id');
        $show->menu_name('Menu name');
        $show->menu_type('Menu type');
        $show->menu_key('Menu key');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MenuModel);

        $form->text('menu_name', 'Menu name');
        $form->text('menu_type', 'Menu type');
        $form->text('menu_key', 'Menu key');

        return $form;
    }
}
