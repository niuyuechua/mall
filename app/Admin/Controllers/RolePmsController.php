<?php

namespace App\Admin\Controllers;

use App\RolePmsModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\PermissionModel;
use App\RoleModel;

class RolePmsController extends Controller
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
//        $grid = new Grid(new RolePmsModel);
//
//        $grid->role_id('Role id');
//        $grid->pms_id('Pms id');
//
//        return $grid;
        $role=RoleModel::get()->toArray();
        $data=PermissionModel::where(['parent_id'=>0])->get()->toArray();
        $pms=[];
        foreach($data as $k=>$v){
            $pms[$v['pms_name']]=PermissionModel::where(['parent_id'=>$v['pms_id']])->get()->toArray();
        }
        //dump($pms);die;
        return view('admin.permission.addPms',compact('role','pms'));
    }

    public function doAdd(){
        $data=request()->all();
        //dump($data);
        $insertData=[];
        foreach($data['pms_id'] as $k=>$v){
            $insertData[]=[
                'role_id'=>$data['role_id'],
                'pms_id'=>$v
            ];
        }
        $res=RolePmsModel::insert($insertData);
        if($res){
            echo '分配成功';
            header("refresh:2;url=/admin/addPms");
        }else{
            echo '分配失败';
            header("refresh:2;url=/admin/addPms");
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
        $show = new Show(RolePmsModel::findOrFail($id));

        $show->role_id('Role id');
        $show->pms_id('Pms id');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new RolePmsModel);

        $form->number('role_id', 'Role id');
        $form->number('pms_id', 'Pms id');

        return $form;
    }
}
