<?php

namespace App\Admin\Controllers;

use App\GoodsModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\CateModel;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
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
//        $grid = new Grid(new GoodsModel);
//
//        $grid->goods_id('Goods id');
//        $grid->goods_name('Goods name');
//        $grid->goods_price('Goods price');
//        $grid->market_price('Market price');
//        $grid->is_up('Is up');
//        $grid->is_new('Is new');
//        $grid->is_best('Is best');
//        $grid->is_hot('Is hot');
//        $grid->goods_num('Goods num');
//        $grid->goods_score('Goods score');
//        $grid->goods_img('Goods img');
//        $grid->goods_imgs('Goods imgs');
//        $grid->goods_desc('Goods desc');
//        $grid->cate_id('Cate id');
//        $grid->brand_id('Brand id');
//        $grid->create_time('Create time');
//
//        return $grid;
        $data=DB::table('goods')
            ->join('cate', 'goods.cate_id', '=', 'cate.cate_id')
            ->select(DB::raw('goods.cate_id,count(*) as count'))
            ->groupBy('goods.cate_id')
            ->get()->toArray();
        //dump($data);die;
        $cate_name='';
        $count='';
        foreach($data as $k=>$v){
            $name=CateModel::where(['cate_id'=>$v->cate_id])->first()->cate_name;
            $cate_name.="'".$name."',";
            $count.=$v->count.",";
        }
        $cate_name=trim($cate_name,",");
        $count=trim($count,",");
        $arr=[
            'cate_name'=>$cate_name,
            'count'=>$count
        ];
        //dump($arr);
        return view('admin.goods.sellNum',$arr);
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(GoodsModel::findOrFail($id));

        $show->goods_id('Goods id');
        $show->goods_name('Goods name');
        $show->goods_price('Goods price');
        $show->market_price('Market price');
        $show->is_up('Is up');
        $show->is_new('Is new');
        $show->is_best('Is best');
        $show->is_hot('Is hot');
        $show->goods_num('Goods num');
        $show->goods_score('Goods score');
        $show->goods_img('Goods img');
        $show->goods_imgs('Goods imgs');
        $show->goods_desc('Goods desc');
        $show->cate_id('Cate id');
        $show->brand_id('Brand id');
        $show->create_time('Create time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new GoodsModel);

        $form->text('goods_name', 'Goods name');
        $form->text('goods_price', 'Goods price');
        $form->decimal('market_price', 'Market price');
        $form->switch('is_up', 'Is up');
        $form->switch('is_new', 'Is new')->default(2);
        $form->switch('is_best', 'Is best')->default(2);
        $form->switch('is_hot', 'Is hot')->default(2);
        $form->number('goods_num', 'Goods num');
        $form->number('goods_score', 'Goods score');
        $form->text('goods_img', 'Goods img');
        $form->text('goods_imgs', 'Goods imgs');
        $form->textarea('goods_desc', 'Goods desc');
        $form->number('cate_id', 'Cate id');
        $form->number('brand_id', 'Brand id');
        $form->number('create_time', 'Create time');

        return $form;
    }
}
