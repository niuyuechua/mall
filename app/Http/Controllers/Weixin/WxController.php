<?php

namespace App\Http\Controllers\Weixin;

use App\MaterialModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\WxUserModel;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use App\GoodsModel;
use App\UserModel;
use App\TmpUserModel;
use App\PhoneModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\ActModel;
use App\LoveModel;

class WxController extends Controller
{
    //首次接入（get方式）
    public function valid(){
        echo $_GET['echostr'];
    }
    //测试专用方法
    public function test(){
        //var_dump($_SERVER['SERVER_NAME']);die;

        //文件缓存
        $filename="assess_token.txt";
        if(file_exists($filename)&&time()-filemtime($filename)<=7200){
            echo '存在';
            $token=file_get_contents($filename);
        }else{
            echo '不存在';
            $token=$this->getAccessToken();
            file_put_contents($filename,$token);
        }
    }
    //微信服务器推送通知
    public function wxEvent(){
        //接收微信服务器推送通知
        $content=file_get_contents('php://input');      //xml数据
        $time=date('Y-m-d H:i:s');
        $str=$time.$content."\n";
        file_put_contents('logs/wx_event.log',$str,FILE_APPEND);
        //echo 'SUCCESS';
        $obj=simplexml_load_string($content);       //将xml数据转化成对象
        //dump($obj);die;
        $kf_id = $obj->ToUserName;  //公众号ID
        $openid=$obj->FromUserName; //用户ID
        $msg_type=$obj->MsgType;    //消息类型（包括事件）
        $event=$obj->Event;    //事件类型（消息类型为事件时，有此字段）
        //全部事件
        if($msg_type=='event'){
            if($event=='subscribe'){
                $event_key=$obj->EventKey;  //场景
                //判断扫描的是普通二维码还是带参数的二维码
                if($event_key==''){
                    //普通二维码
                    $userInfo=WxUserModel::where('openid','=',"$openid")->first();
                    if($userInfo){
                        WxUserModel::where(['openid'=>$openid])->update(['status'=>1]);
                        echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                            <CreateTime>.time().</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA['.'欢迎回来'.$userInfo['nickname'].']]></Content>
                       </xml>';
                    }else{
                        //获取新用户信息
                        $info = $this->getUserInfo($openid);//对象格式
                        //用户信息入库
                        $data=[
                            'openid'=>$info->openid,
                            'nickname'=>$info->nickname,
                            'sex'=>$info->sex,
                            'headimgurl'=>$info->headimgurl,
                            'subscribe_time'=>$info->subscribe_time
                        ];
                        $res = WxUserModel::insert($data);
                        echo '<xml>
                            <ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA['.'欢迎关注'. $info->nickname .']]></Content>
                          </xml>';
                    }
                }else{
                    //带参数的二维码
                    $event_key=substr($event_key,8);    //参数
                    $info = $this->getUserInfo($openid);//对象格式
                    //用户信息入库
                    $data=[
                        'openid'=>$info->openid,
                        'nickname'=>$info->nickname,
                        'sex'=>$info->sex,
                        'headimgurl'=>$info->headimgurl,
                        'subscribe_time'=>$info->subscribe_time,
                        'event_key'=>$event_key
                    ];
                    $res = TmpUserModel::insert($data);
                    $goods=GoodsModel::orderby('create_time','desc')->limit(5)->get()->toArray();
                    foreach($goods as $k=>$v){
                        $img=$v['goods_img'];
                        $picurl="http://1809niuyuechyuang.comcto.com/goodsimg/".$img;
                        $res='<xml>
                          <ToUserName><![CDATA['.$openid.']]></ToUserName>
                          <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                          <CreateTime>'.time().'</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA[欢迎新用户]]></Title>
                              <Description><![CDATA[分享有好礼]]></Description>
                              <PicUrl><![CDATA['.$picurl.']]></PicUrl>
                              <Url><![CDATA[http://1809niuyuechyuang.comcto.com/wx/goodsDetail?goods_id='.$v['goods_id'].']]></Url>
                            </item>
                          </Articles>
                        </xml>';
                        echo $res;
                    }
                }
            }
            if($event=='SCAN'){
                //带参数的二维码
                $goods=GoodsModel::orderby('create_time','desc')->limit(5)->get()->toArray();
                foreach($goods as $k=>$v){
                    $img=$v['goods_img'];
                    $picurl="http://1809niuyuechyuang.comcto.com/goodsimg/".$img;
                    $res='<xml>
                          <ToUserName><![CDATA['.$openid.']]></ToUserName>
                          <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                          <CreateTime>'.time().'</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA[欢迎回来呀]]></Title>
                              <Description><![CDATA[分享有好礼哟]]></Description>
                              <PicUrl><![CDATA['.$picurl.']]></PicUrl>
                              <Url><![CDATA[http://1809niuyuechyuang.comcto.com/wx/goodsDetail?goods_id='.$v['goods_id'].']]></Url>
                            </item>
                          </Articles>
                        </xml>';
                    echo $res;
                }
            }
            if($event=='unsubscribe'){
                WxUserModel::where(['openid'=>$openid])->update(['status'=>0]);
            }
            //点击菜单拉取消息
            $eventKey=$obj->EventKey;   //创建click菜单时设置的key值
            if($event=='CLICK' && $eventKey=='function declaration'){
                $str="你好，欢迎关注！"."\n\n"."发送1 展示全部同学姓名"."\n\n"."发送2 回复最好看同学姓名".
                    "\n\n"."发送图片 可以斗图哟"."\n\n"."发送最新商品 有你想要的哦"."\n\n"."发送小米 查看小米最新神机，也可以发送你喜欢的小米手机哦".
                    "\n\n"."发送城市+天气 查询该城市未来一周天气"."\n\n"."还有性感机器人在线陪聊哦    ~O(∩_∩)O~";
                $this->sendTextMsg($openid,$str);
            }elseif($msg_type=='event'){
                if($event=='CLICK' && $eventKey=='love'){
                    $this->act($openid,"我要表白");
                    $str="请输入你要表白的人的名字";
                    $this->sendTextMsg($openid,$str);
                }elseif($event=='CLICK' && $eventKey=='Looking for love'){
                    $this->act($openid,"查看表白");
                    $str="请输入要查询的人的名字";
                    $this->sendTextMsg($openid,$str);
                }
            }
        }
        //文本消息
        if($msg_type=='text'){
            //表白墙
            $last_act=ActModel::orderBy('act_time','desc')->first();
            $act_time=$last_act->act_time;
            if(time()-$act_time<=60){
                $act_name=$last_act->act_name;
                if($act_name=='我要表白'){
                    //根据用户上一步执行的操作，判断本步执行的操作（输入表白人名字）
                    $this->act($openid,"输入表白人名字");
                    //表白人名字入库
                    $arr=[
                        'openid'=>$openid,
                        'name'=>$obj->Content,
                        'content'=>''
                    ];
                    LoveModel::insert($arr);
                    $str="请输入表白内容";
                    $this->sendTextMsg($openid,$str);
                }elseif($act_name=='输入表白人名字'){
                    //表白内容入库
                    $id=LoveModel::orderBy('id','desc')->first()->id;
                    $content=$obj->Content;
                    LoveModel::where(['id'=>$id])->update(['content'=>$content]);
                    $str="表白成功";
                    $this->sendTextMsg($openid,$str);
                }elseif($act_name=='查看表白'){
                    $name=$obj->Content;
                    $data=LoveModel::where(['name'=>$name])->get();
                    $num=count($data,1);
                    if($num==0){
                        $str="$name 还未被表白";
                    }else{
                        $content='';
                        foreach($data as $k=>$v){
                            $content.=$v['content']."\n";
                        }
                        $str="$name 被表白次数：$num"."\n"."表白内容："."\n".$content;
                    }
                    $this->sendTextMsg($openid,$str);
                }
                die;
            }
            //回复图文
            if($obj->Content=='最新商品') {
                $goods = GoodsModel::orderby('create_time', 'desc')->limit(5)->get()->toArray();
                //$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->getAccessToken()."&type=image";
                foreach ($goods as $k => $v) {
                    $img = $v['goods_img'];
                    $picurl = "http://1809niuyuechyuang.comcto.com/goodsimg/" . $img;
                    $res = '<xml>
                          <ToUserName><![CDATA[' . $openid . ']]></ToUserName>
                          <FromUserName><![CDATA[' . $kf_id . ']]></FromUserName>
                          <CreateTime>' . time() . '</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA[最新商品]]></Title>
                              <Description><![CDATA[啦啦啦]]></Description>
                              <PicUrl><![CDATA[' . $picurl . ']]></PicUrl>
                              <Url><![CDATA[http://1809niuyuechyuang.comcto.com/wx/goodsDetail?goods_id='.$v['goods_id'].']]></Url>
                            </item>
                          </Articles>
                        </xml>';
                    echo $res;
                }
            }
            //回复图文（查询数据库，存在返回详情，不存在返回最新商品详情）
            if(strpos($obj->Content,'小米')){
                $name=$obj->Content;
                $data=PhoneModel::where(['name'=>$name])->first()->toArray();
                //dump($data);
                if(!$data){
                    $data=PhoneModel::orderby('up_time','desc')->first()->toArray();
                }
                $img=$data['img'];
                $picurl="http://1809niuyuechyuang.comcto.com/goodsimg/".$img;
                $res='<xml>
                          <ToUserName><![CDATA['.$openid.']]></ToUserName>
                          <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                          <CreateTime>'.time().'</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA['.$data['name'].']]></Title>
                              <Description><![CDATA[你说小米，我说6 oy]]></Description>
                              <PicUrl><![CDATA['.$picurl.']]></PicUrl>
                              <Url><![CDATA[http://1809niuyuechyuang.comcto.com/wx/phoneDetail?id='.$data['id'].']]></Url>
                            </item>
                          </Articles>
                        </xml>';
                echo $res;
            }
            //查询城市天气
            if(strpos($obj->Content,'+天气')){
                $city=explode('+',$obj->Content)[0];
                //$url="https://free-api.heweather.net/s6/weather/now?location=".$city."&key=HE1905052041271004";
                $url="http://api.k780.com?app=weather.future&weaid=".$city."&appkey=42246&sign=94cfdcf87e9594bdbc981a6e349fd50f&format=json";
                $res=file_get_contents($url);
                $arr=json_decode($res,true);
                if($arr['success']==0){
                    echo '<xml>
                            <ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[城市错误]]></Content>
                          </xml>';
                }elseif($arr['success']==1){
                    $str='';
                    foreach($arr['result'] as $k=>$v){
                        $days=$v['days'];
                        $week=$v['week'];
                        $city=$v['citynm'];// 	该地区／城市的上级城市
                        $tmp=$v['temperature'];//温度
                        $cond_txt=$v['weather'];//天气状况
                        $wind_dir=$v['wind'];//风向
                        $wind_sc=$v['winp'];//风力
                        $str.="日期：".$days. $week."\n"."城市名称：".$city."\n"."温度：".$tmp."\n"."天气状况：".$cond_txt."\n".
                            "风向：".$wind_dir."\n". "风力：". $wind_sc."\n"."\n";
                    }
                    echo '<xml>
                            <ToUserName><![CDATA['.$openid.']]></ToUserName>
                            <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA['.$str.']]></Content>
                          </xml>';
                }
            }
            //回复文本
            $stu_name=[
                '牛月闯',
                '王威龙',
                '张三',
                '李四',
                '王五',
                '赵六',
            ];
            if($obj->Content==1){
                $str=implode("\n",$stu_name);
                $this->sendTextMsg($openid,$str);
            }elseif($obj->Content==2){
                $num=count($stu_name);
                $rand_num=rand(0,$num-1);
                $str=$stu_name[$rand_num];
                $this->sendTextMsg($openid,$str);
            }elseif($obj->Content=='图片'){
                //回复图片
                echo '<xml>
                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                      <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                      <CreateTime>'.time().'</CreateTime>
                      <MsgType><![CDATA[image]]></MsgType>
                      <Image>
                        <MediaId><![CDATA[0OX-rmhHOdhBzCZmOSlTJ-KUIUVRl6L_hWx5O9woHi3raTu8Z5MwcNrpOcg1Pe4Z]]></MediaId>
                      </Image>
                    </xml>';
            }else{
                //机器人
                $info=$obj->Content;
                $url="http://www.tuling123.com/openapi/api?key=1029047843994443a4f7aae786cb3cbe&info=".$info;
                $res=file_get_contents($url);
                $arr=json_decode($res,true);
                $this->sendTextMsg($openid,$arr['text']);
            }
        }
        //图片消息
        if($msg_type=='image'){
            //$media_id=DB::select('SELECT media_id FROM material ORDER BY RAND() LIMIT 1');
            $media_id=MaterialModel::orderByRaw("RAND()")->first()->media_id;
            //dd($media_id);die;
            //回复图片
            echo '<xml>
                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                  <FromUserName><![CDATA['.$kf_id.']]></FromUserName>
                  <CreateTime>'.time().'</CreateTime>
                  <MsgType><![CDATA[image]]></MsgType>
                  <Image>
                    <MediaId><![CDATA['.$media_id.']]></MediaId>
                  </Image>
                </xml>';
        }
    }
    //表白墙记录上一步动作的返回数据
    public function act($openid,$str){
        $arr=[
            'openid'=>$openid,
            'act_name'=>$str,
            'act_time'=>time()
        ];
        ActModel::insert($arr);
    }
    //回复文本消息
    public function sendTextMsg($openid,$str){
        echo '<xml>
                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                  <FromUserName><![CDATA[gh_3174a6d2a0ac]]></FromUserName>
                  <CreateTime>'.time().'</CreateTime>
                  <MsgType><![CDATA[text]]></MsgType>
                  <Content><![CDATA['.$str.']]></Content>
                </xml>';
    }
    //商品详情
    public function goodsDetail(){
        $goods_id=$_GET['goods_id'];
        //echo $goods_id;die;
        $ticket=$this->goodsTicket($goods_id);
        //$code_url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket."";
        $code_url="http://1809a_weixin.com/wx/goodsDetail?goods_id=".$goods_id;
        $goods=GoodsModel::where(['goods_id'=>$goods_id])->first()->toArray();
        $js_config=$this->getConfig();
        $data=[
            'goods'=>$goods,
            'js_config'=>$js_config,
            'code_url'=>$code_url
        ];
        return view('goods.detail',$data);
    }
    //手机详情
    public function phoneDetail(){
        $id=$_GET['id'];
        $phone=PhoneModel::where(['id'=>$id])->first()->toArray();
        $js_config=$this->getConfig();
        $data=[
            'phone'=>$phone,
            'js_config'=>$js_config,
        ];
        return view('phone.detail',$data);
    }
    //获取config接口参数值
    public function getConfig(){
        //获取生成签名的参数
        $ticket=getTicket();
        //echo $ticket;
        $nonceStr=Str::random(10);
        $timestamp=time();
        $current_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        //echo $current_url;die;
        //生成签名
        $string1="jsapi_ticket=$ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$current_url";
        //echo $string1;die;
        $signature=sha1($string1);
        //echo $signature;

        $js_config=[
            'appId'=>env('WX_APP_ID'),  //公众号APPID
            'timestamp'=>$timestamp,    //时间戳
            'nonceStr'=>$nonceStr,     //随机字符串
            'signature'=>$signature,    //签名
        ];
        return $js_config;
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
    //获取新用户信息
    public function getUserInfo($openid){
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->getAccessToken()."&openid=".$openid."&lang=zh_CN";
        $data=file_get_contents($url);
        $u=json_decode($data);
        return $u;
    }

    //创建菜单
    public function createMent(){
        $redirect_url=urlEncode("http://1809niuyuechyuang.comcto.com/wx/getUinfo");
        $url2="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('WX_APP_ID')."&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        //接口数据
        $post_arr=[
            'button' => [
                [
                    'type'=>'click',
                    'name'=>'功能说明',
                    'key'=>'function declaration',
                ],
                [   'name'=>'娱乐',
                    'sub_button'=> [
                        ['type'=>'view',
                            'name'=>'QQ音乐',
                            'url'=>'http://y.qq.com/',
                        ],
                        [
                            'type'=>'view',
                            'name'=>'王者荣耀官网',
                            'url'=>'https://pvp.qq.com/',
                        ],
                    ],
                ],
                [   'name'=>'更多...',
                    'sub_button'=> [
                        ['type'=>'view',
                        'name'=>'最新福利',
                        'url'=>$url2,
                        ],
                        [
                            'type'=>'view',
                            'name'=>'签到',
                            'url'=>$url2,
                        ],
                        [
                            "name"=> "发送位置",
                            "type"=> "location_select",
                            "key"=>"location"
                        ],
                    ],
                ],
            ],
        ];
        $json_str=json_encode($post_arr, JSON_UNESCAPED_UNICODE);   //加参数二可处理含中文的数组
        //dd($json_str);die;
        $url= 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getAccessToken();
        //请求接口
        $client= new Client();
        $responce=$client->request('POST',$url,[
            'body'=>$json_str
        ]);
        //dd($responce);die;
        //处理响应
        //$res_str=$responce->getBody();
        //dd($res_str);
        //$arr=json_decode($res_str,true);
        //dd($arr);
//        if($arr['errcode']==0){
//            echo "创建菜单成功";
//        }else{
//            echo '创建菜单失败';
//        }
    }

    //微信网页授权（手机点击）
    public function authorization(){
        //$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb6e65a6dbd6cfb06&redirect_uri=http%3A%2F%2F1809niuyuechyuang.comcto.com%2Fwx%2FgetUinfo&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
}
    //授权回调
    public function getUinfo(){
        //echo '<pre>';print_r($_GET);echo '</pre>';die;
        $code=$_GET['code'];
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('WX_APP_ID')."&secret=".env('WX_APP_SEC')."&code=".$code."&grant_type=authorization_code";
        $res=json_decode(file_get_contents($url),true);
        //echo '<pre>';print_r($res);echo '</pre>';
        $access_token=$res['access_token'];
        $openid=$res['openid'];
        $url2="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $userInfo=json_decode(file_get_contents($url2),true);
        //echo '<pre>';print_r($userInfo);echo '</pre>';
        //用户信息入库
        $user=UserModel::where(['openid'=>$userInfo['openid']])->first();
        if($user){
            echo '欢迎回来 '.$user['nickname'].',正在跳转至福利页面';
            header("refresh:3;url=http://1809niuyuechyuang/wx/goodsDetail?goods_id=60");
        }else{
            //用户信息入库
            $user=[
                'openid'=>$userInfo['openid'],
                'nickname'=>$userInfo['nickname'],
                'sex'=>$userInfo['sex'],
                'language'=>$userInfo['language'],
                'city'=>$userInfo['city'],
                'province'=>$userInfo['province'],
                'country'=>$userInfo['country'],
                'headimgurl'=>$userInfo['headimgurl']
            ];
            $r=UserModel::insert($user);
            if($r){
                echo '欢迎'.$userInfo['nickname'].',正在跳转至福利页面';
                header("refresh:3;url=http://1809niuyuechyuang/wx/goodsDetail?goods_id=60");
            }else{
                echo '授权失败,请稍后尝试';
            }

        }
    }

    //获取生成带参数二维码的ticket
    public function getTicket(){
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->getAccessToken()."";
        $arr=[
            'expire_seconds'=> 604800,
            'action_name'=>'QR_SCENE',
            'action_info'=>[
                'scene'=>[
                    'scene_id'=>666
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
        //dump($json);
        $arr2=json_decode($json,true);
        dump($arr2);
        $ticket=$arr2['ticket'];
        $code_url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
        //$code_url="http://1809a_weixin.com/wx/valid";
        $data=[
            'code_url'=>$code_url
        ];
        return view('code.code',$data);
    }
    //根据商品id获取生成带参数二维码的ticket
    public function goodsTicket($goods_id){
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->getAccessToken()."";
        $arr=[
            'expire_seconds'=> 604800,
            'action_name'=>'QR_SCENE',
            'action_info'=>[
                'scene'=>[
                    'scene_id'=>$goods_id
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
        //dump($json);
        $arr2=json_decode($json,true);
        //dump($arr2);
        $ticket=$arr2['ticket'];
        return $ticket;
    }
}
