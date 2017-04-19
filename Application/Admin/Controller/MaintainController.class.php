<?php
namespace Admin\Controller;
use Think\Controller;
class MaintainController extends Controller{
	public function __construct(){
        parent::__construct();
        $msg=session('admininfo');
        $prid=$msg['pridlist'];
        if (!in_array(2,$prid)){
            $this->error('您无此权限！');
        }
    }
	public function index(){
		$date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());
        // 在header显示系统当前登录的用户名
        $user=mb_substr($_SESSION['admininfo']['uname'],0,4).'***';
        $msg=session('admininfo');
        $this->assign(array('curuser'=>$user,'prid'=>$msg['pridlist'],'curdate'=>$date));
		$this->display();
	}
}