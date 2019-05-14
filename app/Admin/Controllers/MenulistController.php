<?php

namespace App\Admin\Controllers;

use App\MenuModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;

class MenulistController extends Controller
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
            ->body($this->grid())
            ->row($this->button())     //一键同步按钮
            ->breadcrumb(
                ['text' => '首页管理', 'url' => '/admin'],
                ['text' => '菜单列表', 'url' => '/admin']
            );
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
        $grid = new Grid(new MenuModel);

        $grid->id('Id');
        $grid->menu_name('菜单名称');
        $grid->menu_type('菜单类型');
        $grid->menu_key('菜单标识');
        $grid->parent_id('菜单级别')->display(function($parent_id){
            if($parent_id==0){
                return "一级菜单";
            }else{
                return "二级菜单";
            }
        });

        return $grid;
    }
    //一键同步按钮
    public function button(){
        return view('admin.menu.button');
    }
    //同步微信菜单
    public function createMenu(){
        $menu_data=MenuModel::where(['parent_id'=>0])->get()->toArray();
        //dump($menu_data);
        $post_arr=[];
        //优化
        $type_arr=[
            'click'=>'key',
            'view'=>'url',
            'location_select'=>'key'
        ];
        foreach($menu_data as $k=>$v){
            $sc_menu=MenuModel::where(['parent_id'=>$v['id']])->get()->toArray();
            if($sc_menu){
                MenuModel::where(['id'=>$v['id']])->update(['menu_type'=>'','menu_key'=>'']);
                $post_arr['button'][$k]['name']=$v['menu_name'];
                foreach($sc_menu as $key=>$value){
                    $post_arr['button'][$k]['sub_button'][]=[
                        'type'=>$value['menu_type'],
                        'name'=>$value['menu_name'],
                        $type_arr[$value['menu_type']]=>$value['menu_key'],
                    ];
                }
            }else{
                $post_arr['button'][]=[
                    'type'=>$v['menu_type'],
                    'name'=>$v['menu_name'],
                    $type_arr[$v['menu_type']]=>$v['menu_key'],
                ];
            }
        }
        //dump($post_arr);die;
        $json_str=json_encode($post_arr, JSON_UNESCAPED_UNICODE);
        $url= 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.getAccessToken();
        //请求接口
        $client= new Client();
        $res=$client->request('POST',$url,[
            'body'=>$json_str
        ]);
        //处理响应
        $res_str=$res->getBody();
        $arr=json_decode($res_str,true);
        //dump($arr);
        if($arr['errcode']==0){
            echo '同步微信菜单成功';
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
