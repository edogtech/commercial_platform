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
        $this->display();
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



}