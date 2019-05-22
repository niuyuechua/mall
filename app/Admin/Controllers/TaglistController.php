<?php

namespace App\Admin\Controllers;

use App\TagModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\WxUserModel;
use GuzzleHttp\Client;

class TaglistController extends Controller
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
        $grid = new Grid(new TagModel);

        $grid->t_id('T id');
        $grid->tag_name('标签名称');
        $grid->tag_id('微信标签标识');

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
//        $show = new Show(TagModel::findOrFail($id));
//
//        $show->t_id('T id');
//        $show->tag_name('Tag name');
//        $show->tag_id('Tag id');
//
//        return $show;
        $t_id=$id;
        $data=WxUserModel::get()->toArray();
        $tag_id=TagModel::where(['t_id'=>$t_id])->value('tag_id');
        //获取标签下粉丝列表
        $url="https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=".getAccessToken();
        $json_data='{"tagid":"'.$tag_id.'"}';
        $client=new Client();
        $res=$client->request('POST',$url,[
            'body'=>$json_data
        ]);
        $json_res=$res->getBody();
        $arr_res=json_decode($json_res,true);
        //dump($arr_res);die;
        $openid=[];
        if($arr_res['count']>0){
            $openid=$arr_res['data']['openid'];
        }

//        $user_id=[];
//        foreach($data as $k=>$v){
//            // 获取用户身上的标签列表
//            $url="https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=".getAccessToken();
//            $json_data='{"openid" :"'.$v['openid'].'"}';
//            $client=new Client();
//            $res=$client->request('POST',$url,[
//                'body'=>$json_data
//            ]);
//            $json_res=$res->getBody();
//            $arr_res=json_decode($json_res,true);
//            //当用户关注后，但未被分配标签时，此数据需做判断
//            dump($arr_res);
//            $tagid=$arr_res["tagid_list"];
//            $tag_id=TagModel::where(['t_id'=>$t_id])->value('tag_id');
//            if(in_array($tag_id,$tagid)){
//                $user_id[]=$v['id'];
//            }
//        }
        return view('admin.tag.makeTag',compact('data','t_id','openid'));
    }
    public function makeTag(){
        $openid=$_GET['openid'];
        $t_id=$_GET['t_id'];
        $tag_id=TagModel::where(['t_id'=>$t_id])->value('tag_id');
        $url="https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=".getAccessToken();
//        $post_data=[
//            'openid_list'=>$openid,
//            'tagid'=>$tag_id
//        ];
//        $json_data=json_encode($post_data,JSON_UNESCAPED_UNICODE);
        $json_data='{
            "openid_list" : ['.$openid.'],   
            "tagid" : '.$tag_id.'
        }';
        $client=new Client();
        $res=$client->request('POST',$url,[
            'body'=>$json_data
        ]);
        $json_res=$res->getBody();
        $arr_res=json_decode($json_res,true);
        //dump($arr_res);
        if($arr_res['errcode']==0){

            echo '1';
        }else{
            echo '2';
        }
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
    public function destroy($id){
        $res=TagModel::destroy($id);
        $url="https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=".getAccessToken();
        $post_data='{   "tag":{   "id" :'.$id.'} }';
        $client=new Client();
        $res=$client->request('POST',$url,[
            'body'=>$post_data
        ]);
        $json_res=$res->getBody();
        $arr_res=json_decode($json_res,true);
        if($res&&$arr_res['errcode']==0){
            echo '删除成功';
        }
    }
}
