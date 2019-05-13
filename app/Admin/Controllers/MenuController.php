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
        $info=MenuModel::where(['parent_id'=>0])->get()->toArray();
        $menu=[
            'info'=>$info
        ];
        return view('admin.menu.addMenu',$menu);
    }
    public function addMenu(Request $request){
        $data=$request->input();
        //判断菜单信息
        $info=MenuModel::where(['parent_id'=>0])->get();
        if($data['parent_id']==0){      //一级菜单
            //判断菜单名称长度
            if(strlen($data['menu_name'])>12){
                echo "一级菜单名最多4个汉字";die;
            }
            //判断菜单个数
            $num=count($info,1);
            if($num>=3){
                echo "一级菜单不能超过3个";die;
            }
        }else{                           //二级菜单
            //判断菜单名称长度
            if(strlen($data['menu_name'])>21){
                echo "二级菜单名最多7个汉字";die;
            }
            //判断菜单个数
            foreach($info as $k=>$v){
                $sc_info=MenuModel::where(['parent_id'=>$v['id']])->get();
                $sc_num=count($sc_info);
                if($sc_num>=5){
                    echo "二级菜单不能超过5个";die;
                }
            }
        }
        //菜单信息入库
        $data=[
            'menu_name'=>$data['menu_name'],
            'menu_type'=>$data['menu_type'],
            'menu_key'=>$data['menu_key'],
            'parent_id'=>$data['parent_id'],
        ];
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
