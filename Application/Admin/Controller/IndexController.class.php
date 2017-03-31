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

        $userinfo=M("user_info")->where("uphone={$phone} and level=1")->find();

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



        $id=$_POST['id'];
        $this->assign('id',$id);
        $this->display();
    }

    public function modify_password(){

    }

    public function register1(){
        $this->display();
    }

    public function register2(){
        $this->display();
    }

    public function register3(){
        $this->display();
    }

    public function register4(){
        $this->display();
    }

    public function login(){
        $username=trim($_POST['username']);
        $password=trim($_POST['password']);
        $code=trim($_POST['code']);
        $auto_login=intval($_POST['auto_login']);

        $verify = new \Think\Verify();
        $verity_check=$verify->check($code);
        if(!$verity_check){
            $this->redirect('Index/index', array('error' => 1));
        }

        $userinfo=M("user_info")->where("uname='{$username}' and level=1")->find();
        if(!$userinfo){
            $this->redirect('Index/index', array('error' => 2));
        }

        if(md5($password)==$userinfo['upswd']){

            if($auto_login==1){
                $_SESSION['userinfo']=$userinfo;
                $_SESSION['userinfo']['endtime']=time()+86400;
                unset ($_SESSION['userinfo']['upswd']);
            }else{
                $_SESSION['userinfo']=$userinfo;
                $_SESSION['userinfo']['endtime']=time()+86400;
                unset ($_SESSION['userinfo']['upswd']);
            }

            dump($_SESSION);

        }else{
            $this->redirect('Index/index', array('error' => 3));
        }


    }

    public function  send_text(){
        require_once  '/PUBLIC/jsms-api-php-client-master/src/JSMS.php';

        $appKey = '459d9bee0bac542534a6fd57';
        $masterSecret = '35e380846e272cfe8ca678ee';
        $phone = '15836117462';


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