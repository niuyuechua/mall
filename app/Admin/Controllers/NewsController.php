<?php

namespace App\Admin\Controllers;

use App\WxUserModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;

class NewsController extends Controller
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
        $user=WxUserModel::all();
        $data=[
            'user'=>$user
        ];
        return view('admin.news',$data);
    }
    //群发消息
    public function sendMessage(){
        $client=new Client();
        $openid=$_GET['openid'];
        $text=$_GET['text'];
        $openid=explode(',',$openid);
        //dd($openid);
        $arr=[
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => [
                'content'=>$text
            ]
        ];
        $str=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.getAccessToken();
        $response=$client->request('POST',$url,[
            'body'=>$str
        ]);
        $json =  $response->getBody();
        $arr_res=json_decode($json,true);
        //dump($arr_res);
        if($arr_res['errcode']==0){
            echo 'success';
        }else{
            echo 'error';
        }
    }
    //根据标签进行群发（测试）
    public function test(){
        $url="https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=".getAccessToken();
        $arr=[
            "filter"=>[
            "is_to_all"=>true,
           ],
           "text"=>[
                    "content"=>"卫龙辣条真好吃"
           ],
            "msgtype"=>"text"
        ];
        $json_str=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $client=new Client();
        $res=$client->request('POST',$url,[
            'body'=>$json_str
        ]);
        $json_res=$res->getBody();
        $arr_res=json_decode($json_res,true);
        dump($arr_res);
    }
    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WxUserModel::findOrFail($id));

        $show->id('Id');
        $show->uid('Uid');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');
        $show->unionid('Unionid');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WxUserModel);

        $form->number('uid', 'Uid');
        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('nickname', 'Nickname');
        $form->switch('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');
        $form->text('unionid', 'Unionid');

        return $form;
    }
}
