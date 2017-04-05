<?php
namespace Admin\Controller;
use Think\Controller;
class FinancialController extends Controller {
    //关于我们页面的展示
    public function index(){
        /*header*/
        // 在header显示系统当前时间
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());

        // 在header显示系统当前登录的用户名
        $user=mb_substr($_SESSION['admininfo']['username'],0,4).'**';

        //订单数据
        $count=M('charge_order')->where()->count();
        $page= new \Think\Page($count,5);
        $show=$page->show();
        $order=M('charge_order')->order('id desc')->where()->limit($page->firstRow,$page->listRows)->select();

        $month_now=date('Ym',time());
        //统计数据
        $month_order=M()->query("select count(*) as count from charge_order where FROM_UNIXTIME(addtime,'%Y%m')=".$month_now);
        $month_sum=M()->query("select SUM(charge_price) as sum from charge_order where FROM_UNIXTIME(addtime,'%Y%m')=".$month_now);
        $totle['order']=$month_order[0]['count'];
        $totle['sum']=$month_sum[0]['sum'];

        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->assign('show',$show);
        $this->assign('lists',$order);
        $this->assign('totle',$totle);
        $this->display();
    }
    
}
