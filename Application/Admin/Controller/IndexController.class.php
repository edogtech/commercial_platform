<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){

       $this->display();
    }

    public function find_password1(){
       $this->display();
    }

    public function find_password2(){
        if(!isset($_POST['phone']) || !isset($_POST['code'])){
            $this->redirect('Index/find_password1');
        }

        $phone=trim($_POST['phone']);
        $code=intval($_POST['code']);

        $verify = new \Think\Verify();
        $verity_check=$verify->check($code);
        if(!$verity_check){
            $this->redirect('Index/find_password1', array('error' => 1));
        }

        $userinfo=M("user_merchant")->where("uphone={$phone}")->find();

        if(!$userinfo){
            $this->redirect('Index/find_password1', array('error' => 2));
        }

        $this->assign('userinfo',$userinfo);
        $this->display();
    }

    public function find_password3(){
        if(!isset($_POST['id']) || !isset($_POST['code'])){
            $this->redirect('Index/find_password1');
        }

        $code=trim($_POST['code']);
        $limit_time=time() - $_SESSION['phone_code']['time'];
        if($limit_time>180){
            $this->redirect('Index/find_password1', array('error' => 4));
        }
        if($_SESSION['phone_code']['code']!=$code){
            $this->redirect('Index/find_password1', array('error' => 3));
        }

        $id=$_POST['id'];
        $this->assign('id',$id);
        $this->display();
    }

    public function modify_password(){
        $id=intval($_POST['id']);
        $password=trim($_POST['password']);
        $repassword=trim($_POST['repassword']);

        if($password != $repassword){
            $this->error('两次密码不一致');
        }
        $data['upswd']=md5($password);
        $res=M('user_merchant')->where("uid={$id}")->save($data);
        if($res){
            $this->redirect('Index/index', array('error' => 3),3, "密码修改成功，三秒后跳往登录页面，如没有自动跳转请<a href=index>点击这里</a>");
        }else{
            $this->error('修改失败');
        }


    }

    public function register1(){
        $this->display();
    }

    public function register2(){
        if(!isset($_POST['phone']) || !isset($_POST['code'])  || !isset($_POST['phone_code'])){
            $this->redirect('Index/register1');
        }

        $phone=trim($_POST['phone']);
        $code=intval($_POST['code']);
        $phone_code=intval($_POST['phone_code']);

        $verify = new \Think\Verify();
        $verity_check=$verify->check($code);
        if(!$verity_check){
            $this->redirect('Index/register1', array('error' => 1));
        }


        $limit_time=time() - $_SESSION['phone_code']['time'];
        if($limit_time>180){
            $this->redirect('Index/register1', array('error' => 4));
        }
        if($_SESSION['phone_code']['code']!=$phone_code){
            $this->redirect('Index/register1', array('error' => 3));
        }

        $res=M('user_merchant')->where("uphone={$phone}")->find();
        if($res){
            $this->redirect('Index/register1', array('error' => 2));
        }else{
            $_SESSION['register_info']['phone']=$phone;
            $this->display();
        }

    }

    //ajax验证账号是否被占用
    public function check_username_unique(){
        $username=trim($_POST['username']);
        $res=M("user_merchant")->where("uname='{$username}'")->find();
        if(empty($res)){
            echo 'yes';
        }else{
            echo 'no';
        }


    }

    public function register3(){
        if(!isset($_POST['username']) || !isset($_POST['password'])  || !isset($_POST['repassword'])){
            $this->redirect('Index/register1');
        }

        $username=trim($_POST['username']);
        $password=trim($_POST['password']);
        $repassword=trim($_POST['repassword']);

        if($password!=$repassword){
            $this->error('两次密码不一致');
        }


        $res=M("user_merchant")->where("uname='{$username}'")->find();
        if(!empty($res)){
            $this->error('用户名已被占用');
        }

        $_SESSION['register_info']['username']=$username;
        $_SESSION['register_info']['password']=md5($password);
        $this->display();
    }

    public function register4(){
        if(!isset($_POST['company']) || !isset($_POST['email'])  || !isset($_POST['licence'])){
            $this->redirect('Index/register1');
        }
        if(!isset($_SESSION['register_info']['password']) || !isset($_SESSION['register_info']['phone'])  || !isset($_SESSION['register_info']['username'])){
            $this->redirect('Index/register1');
        }
        //先验证邮箱唯一
        $email=trim($_POST['email']);
        $email_res=M('user_merchant')->where("email='{$email}'")->find();
        if($email_res){
            $this->error('邮箱已经被占用');
        }

        //处理照片上传
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     5242880 ;// 设置附件上传大小 5m
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');// 设置附件上传类型
        $upload->rootPath  =      './Public/'; // 设置附件上传根目录
        $upload->savePath  =      'Uploads/'; // 设置附件上传目录
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {
            // 上传错误提示错误信息
            $this->error($upload->getError());
        }else{
            $data['licence_pic']='/Public/'.$info['pic']['savepath'].$info['pic']['savename'];
        }

        //加入其它信息
        $data['email']=$email;
        $data['company']=trim($_POST['company']);
        $data['b_licence']=trim($_POST['licence']);
        $data['uname']=$_SESSION['register_info']['username'];
        $data['upswd']=$_SESSION['register_info']['password'];
        $data['uphone']=$_SESSION['register_info']['phone'];
        $data['addtime']=time();

        $res=M('user_merchant')->add($data);
        if($res){
            $this->display();
        }else{
            $this->error('注册失败，请稍后再试');
        }
    }

    public function login(){
        $username=trim($_POST['username']);
        $password=trim($_POST['password']);
        $code=trim($_POST['code']);
        $auto_login=intval($_POST['auto_login']);

        $verify = new \Think\Verify();
        $verity_check=$verify->check($code);
        /*if(!$verity_check){
            $this->redirect('Index/index', array('error' => 1));
        }*/
        //登录管理2017-04-07
        if (stristr($username,'@')) {
            $userinfo=D('UserInfo')->where(array('uname'=>$username))->find();
            if(!$userinfo){
                $this->redirect('Index/index', array('error' => 2));
            }
            if(md5($password)==$userinfo['upswd']){

                if($auto_login==1){
                    $_SESSION['admininfo']=$userinfo;
                    $_SESSION['admininfo']['endtime']=time()+86400;
                    unset ($_SESSION['admininfo']['upswd']);
                }else{
                    $_SESSION['admininfo']=$userinfo;
                    $_SESSION['admininfo']['endtime']=time()+86400;
                    unset ($_SESSION['admininfo']['upswd']);
                }
                $muid=$userinfo['uid'];
                $pridlist=D('PrivRelation')->field('privilegeid')->where(array('uid'=>$muid))->find();
                $substrpid=substr($pridlist['privilegeid'],0,1);
                $prid=explode(',',$pridlist['privilegeid']);
                $_SESSION['admininfo']['pridlist']=$prid;
                print_r(session('admininfo'));die;
                $this->assign('prid',$prid);
                $shows=D('PrivInfo')->field('paction')->where(array('pid'=>$substrpid))->find();
                $this->assign('prid',$prid);
                $showdisp=$shows['paction'];
                $this->redirect("$showdisp");

            }else{
                $this->redirect('Index/index', array('error' => 3));
            }
        }else{
            $userinfo=M("user_merchant")->where("uname='{$username}'")->find();
            if(!$userinfo){
                $this->redirect('Index/index', array('error' => 2));
            }else{
                $userinfo['identify']=$userinfo['uid'];
            }

            if(md5($password)==$userinfo['upswd']){

                if($auto_login==1){
                    $_SESSION['admininfo']=$userinfo;
                    $_SESSION['admininfo']['endtime']=time()+86400;
                    unset ($_SESSION['admininfo']['upswd']);
                }else{
                    $_SESSION['admininfo']=$userinfo;
                    $_SESSION['admininfo']['endtime']=time()+86400;
                    unset ($_SESSION['admininfo']['upswd']);
                }
                $prid1=$userinfo['privilegeid'];
                $prid=explode(',',$prid1);
                $_SESSION['admininfo']['pridlist']=$prid;
                //print_r(session('admininfo'));die;
                //展示页
                $strpid=substr($prid1,0,1);
                $show=D('PrivInfo')->field('paction')->where(array('pid'=>$strpid))->find();
                $this->assign('prid',$prid);
                $showdis=$show['paction'];
                $this->redirect("$showdis");

            }else{
                $this->redirect('Index/index', array('error' => 3));
            }
        }
        
    }

    public function  send_text(){
        require_once  './Public/jsms-api-php-client-master/src/JSMS.php';

        $appKey = '459d9bee0bac542534a6fd57';
        $masterSecret = '35e380846e272cfe8ca678ee';
        $phone = trim($_POST['send_phone']);

        //防止在控制台连续调用短时间内多次发送
        if(isset($_SESSION['phone_code'])){
            $limit_time=time() - $_SESSION['phone_code']['time'];
            if($limit_time<50){
                echo '不能短时间内多次发送';
                exit;
            }
        }


        $code=mt_rand(111111,999999);
        $_SESSION['phone_code']=['time'=>time(),'code'=>$code];
        //这里的 $temp_id 和 $temp_para 的值需要到 "极光控制台 -> 短信验证码 -> 模板管理" 里面获取
        $temp_id = '37524';
        $temp_para = ['code' => $code];

        $client = new \JiGuang\JSMS($appKey, $masterSecret,[ 'ssl_verify' => false ]);
        $response = $client->sendMessage($phone, $temp_id, $temp_para);

        if($response['http_code']==200){
            echo 'success';
        }else{
            echo 'fail';
        }

    }



}