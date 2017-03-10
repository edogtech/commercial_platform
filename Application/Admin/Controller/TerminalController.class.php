<?php
namespace Admin\Controller;
use Think\Controller;
class TerminalController extends Controller {

    //关于我们页面的展示
    public function index(){
        
        // 在header显示系统当前时间
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());
        
        // 在header显示系统当前用户
        $user='我心飞翔';

        $ob=M('electric_pile');
        $field='e_number';
        $pile['totalQty']=$ob->distinct(true)->field($field)->count(); // 桩总数
        $pile['dcQty']=$ob->where('type=29')->count(); // 直流桩数量
        $pile['acQty']=$ob->where('type=30')->count(); // 交流桩数量
        
        // 今日充电次数
        $times=$ob->field('sum(times) as times')->find(); 
        $pile['times']=$times['times']; 
        
        // 状态
        $pile['occupyQty']=$ob->where("`use`=0")->count(); // 工作中
        $pile['idleQty']=$ob->where("`use`=1")->count(); // 空闲
        $pile['faultQty']=$ob->where("`use`=2")->count(); // 故障
        

        

        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->assign('pilesinfo',$pile);
        $this->display();
    }
    

}