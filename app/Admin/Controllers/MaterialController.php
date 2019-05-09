<?php

namespace App\Admin\Controllers;

use App\MaterialModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class MaterialController extends Controller
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
//        $grid = new Grid(new MaterialModel);
//
//        $grid->id('Id');
//        $grid->type('Type');
//        $grid->media_id('Media id');
//        $grid->create_at('Create at');
//
//        return $grid;
        return view('admin.addImg');
    }

    public function addImg(Request $request){
        $fileInfo=$request->file('file');
        //dump($fileInfo);
        $filename=$fileInfo->getClientOriginalName();   //原始文件名
        $ext=$fileInfo->getClientOriginalExtension();   //原始文件扩展名
        //生成新文件名 （规则：时间+随机字符串+原始文件扩展名）
        $new_filename=date('ymd').'_'.Str::random(10).'.'.$ext;
        $save_path='upload';
        //保存文件（lavarel上传文件）
        $res=$fileInfo->storeAs($save_path,$new_filename);      //默认保存在 storage/app/$save_path
        //var_dump($res);die;       //返回文件保存路径
        $access_token=$this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$access_token.'&type=image';

          //curl上传临时素材
//        $res="/wwwroot/mall/public/".$res;
//        $imgPath=new \CURLFile($res);
//        $post_data=[
//            'media'=>$imgPath
//        ];
//        $data=$this->curlPost($url,$imgPath);
//        dump($data);die;

        //guzzle上传临时素材
        $client = new Client();
        $response = $client->request('post',$url,[
            'multipart' => [
                [
                    'name' => 'filename',
                    'contents' => fopen($res, 'r'),
                ]
            ]
        ]);
        $json =  $response->getBody();
        $arr=json_decode($json,true);
        //dump($arr);die;
        $arr['img_url']=$res;
        $res2=MaterialModel::insert($arr);
        if($res2){
            echo '素材上传成功';
        }else{
            echo '素材上传失败';
        }
    }
    //curl上传临时素材
    public function curlPost($url,$post_data)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }
    //获取access_token
    public function getAccessToken(){
        $key="set_access_token";
        $data=Redis::get($key);
        if($data){
            //echo "有缓存";
        }else{
            //echo "没有缓存";
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxb6e65a6dbd6cfb06&secret=9fdf084e4ff69341e638e2e7941e8ce8";
            $response=file_get_contents($url);      //json字符串
            $arr=json_decode($response,true);       //将json字符串转化成数组
            //做缓存
            Redis::set($key,$arr['access_token']);
            Redis::expire($key,3600); //设置时间（30）
            $data=$arr['access_token'];
        }
        return $data;
    }
    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(MaterialModel::findOrFail($id));

        $show->id('Id');
        $show->type('Type');
        $show->media_id('Media id');
        $show->create_at('Create at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MaterialModel);

        $form->text('type', 'Type');
        $form->number('media_id', 'Media id');
        $form->number('create_at', 'Create at');

        return $form;
    }
}
