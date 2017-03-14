<?php
namespace Admin\Controller;
use Think\Controller;
class TerminalController extends Controller {
    //关于我们页面的展示
    public function index(){
        
        // 在header显示系统当前时间
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());
        
        // 在header显示系统当前登录的用户名
		$user=mb_substr($_SESSION['admininfo']['username'],0,4).'***';
        $ob=M('charge_pile');
        $field='*';
        $pile['totalQty']=$ob->field($field)->count(); // 桩总数
        $pile['dcQty']=$ob->where('type=0')->count(); // 直流桩数量
        $pile['acQty']=$ob->where('type=1')->count(); // 交流桩数量
        
        // 今日充电次数
        $times=$ob->field('sum(times_today) as times')->find(); 
        $pile['times']=$times['times']; 
        
        // 状态,注意status值为枚举类型
        $pile['occupyQty']=$ob->where("status='0'")->count(); // 工作中    
        $pile['idleQty']=$ob->where("status='1'")->count(); // 空闲
        $pile['faultQty']=$ob->where("status='2'")->count(); // 故障
        
        $ob_station=M('charge_station');
        $field='id,name,ac_num+dc_num as pile_total,electricity_history,times_today';
        $re_station=$ob_station->field($field)->select();

        $ob_pile=M('charge_pile');
        //每个电站中各桩的状态统计
        foreach ($re_station as $k=>$v) {
            $stationID=$re_station[$k]['id'];
            $re_station[$k]['sequence']=$k+1;
            $re_station[$k]['pile_occupy']=$ob_pile->where("station_id=$stationID AND status='0'")->count();
            $re_station[$k]['pile_idle']=$ob_pile->where("station_id=$stationID AND status='1'")->count();
            $re_station[$k]['pile_fault']=$ob_pile->where("station_id=$stationID AND status='2'")->count();
            // $pile_status[$k]=$ob_pile->where($where)->field("count(status) as num")->group("status")->select();
        }

        
        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->assign('pilesinfo',$pile);

        $this->assign('lists',$re_station);
        $this->display();
    }
    
}