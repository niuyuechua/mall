<?php

namespace App\Admin\Controllers;

use App\ChannelModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class ChannelController extends Controller
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
        return view('admin.channel.addChannel');
    }
    public function addChannel(Request $request){
        $channel_name=$request->channel_name;
        $channel_sign=$request->channel_sign;
        $ticket=$this->getTicket($channel_sign);
        $url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
        $qrcode_url='qrcode/'.date('ymd').'_'.Str::random(10).'.'.'jpg';
        copy($url,$qrcode_url);
        $arr=[
            'channel_name'=>$channel_name,
            'channel_sign'=>$channel_sign,
            'qrcode_url'=>$qrcode_url
        ];
        $res=ChannelModel::insert($arr);
        if($res){
            echo '渠道添加成功';
            header("Refresh:2;url=/admin/channellist");
        }else{
            echo '渠道添加失败';
            header("Refresh:2;url=/admin/channel");
        }
    }
    public function getTicket($channel_sign){
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".getAccessToken()."";
        $arr=[
            'expire_seconds'=> 604800,
            'action_name'=>'QR_SCENE',
            'action_info'=>[
                'scene'=>[
                    'scene_id'=>$channel_sign
                ]
            ]
        ];
        $str=json_encode($arr);
        //dump($str);
        $client = new Client();
        $response = $client->request('POST',$url,[
            'body' => $str
        ]);
        $json =  $response->getBody();
        $arr2=json_decode($json,true);
        //dump($arr2);
        $ticket=$arr2['ticket'];
        return $ticket;
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
