<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {

	//登录页面的展示
    public function index(){
        $this->display();
    }

    //验证码
    public function code(){
		ob_clean();
    	$Verify = new \Think\Verify();
    	$Verify->fontSize = 24;
    	$Verify->length   = 4;
    	$Verify->useImgBg = true; 
    	$Verify->codeSet = '0123456789'; 
    	$Verify->imageW = 200; 
    	$Verify->imageH = 50; 

    	$Verify->entry();
    }


    public function chkLogin(){

    	if(in_array("",$_POST)){
    		$this->error("请输入完整的登录信息");
    	}

    	//验证验证码
    	$verify = new \Think\Verify();    
    	if($verify->check($_POST['code'])){
    		//通过验证处理账号密码
    		$_POST["username"]=trim($_POST["username"]);
    		$_POST["password"]=md5(trim($_POST["password"]));
    	}else{
    		$this->error("验证码输入错误");
    	}

    	$map["user"]=$_POST["username"];
    	$map["password"]=$_POST["password"];
// $this->success("登陆成功",U("Terminal/index"));die;
//     	$ob=M("e_zc_sysuser");
//     	$res=$ob->where($map)->find();
    	
    	if(1){

    		if($res['status']==1){
    			$this->error("此账户已被禁用");
    		}
    		//记录登录信息
    		$data['last_login_ip']=$_SERVER['REMOTE_ADDR'];//获取客户IP
    		$data['login_count']=$res['login_count']+1;//登录次数
    		$data['last_login_time']=time();//登录时间
//     		$re=$ob->data($data)->where($map)->save();//更新登录信息
    		
//     		session("admininfo",$res);
    		session("admininfo",$data);
    		$this->success("登陆成功",U("Terminal/index"));
    	}else{
    		$this->error("登录失败,账号或密码有误");
    	}

    }


    public function logout(){
    	session(null);
    	$this->success("注销成功,跳转登录页面",U("Login/index"));
    }




}