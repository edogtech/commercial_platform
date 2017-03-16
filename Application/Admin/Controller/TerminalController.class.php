<?php
namespace Admin\Controller;
use Think\Controller;
class TerminalController extends Controller {
    //关于我们页面的展示
    public function index(){
/*header*/        
        // 在header显示系统当前时间
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());
        
        // 在header显示系统当前登录的用户名
		$user=mb_substr($_SESSION['admininfo']['username'],0,4).'***';

/*状态栏*/
        $ob=M('charge_pile');
        $field='*';
        $pile['totalQty']=$ob->field($field)->count(); // 桩总数
        $pile['dcQty']=$ob->where('type=0')->count(); // 直流桩数量
        $pile['acQty']=$ob->where('type=1')->count(); // 交流桩数量
      
        // 今日充电次数
        $times=$ob->field('sum(times_today) as times')->find(); 
        $pile['times']=$times['times']; 
        
        // 注意status值为枚举类型
        $pile['occupyQty']=$ob->where("status='0'")->count(); // 工作中    
        $pile['idleQty']=$ob->where("status='1'")->count(); // 空闲
        $pile['faultQty']=$ob->where("status='2'")->count(); // 故障
        
        
/*列表及分页*/
        $station=I('post.txtStation','','trim');
        if(!empty($station)){
            $where['name']=array('like',"%{$station}%");
        }
        
        $ob_station=M('charge_station');
        $field='id,name,ac_num+dc_num as pile_total,electricity_history,times_today';
          
        //取得总条数
        $count=$ob_station->where($where)->count();
        //根据总条数实例化page类
        $page= new \Think\Page($count,8);
        //分页显示输出
        $show= $page->show();
        $list = $ob_station->field($field)->where($where)->limit($page->firstRow,$page->listRows)->select();
        
        //每个电站中桩的状态统计
        $ob_pile=M('charge_pile');
        foreach ($list as $k=>$v) {
            $stationID=$list[$k]['id'];
            $list[$k]['sequence']=$k+1;
            $list[$k]['pile_occupy']=$ob_pile->where("station_id=$stationID AND status='0'")->count();
            $list[$k]['pile_idle']=$ob_pile->where("station_id=$stationID AND status='1'")->count();
            $list[$k]['pile_fault']=$ob_pile->where("station_id=$stationID AND status='2'")->count();
            // $pile_status[$k]=$ob_pile->where($where)->field("count(status) as num")->group("status")->select();
        }
        
        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->assign('pilesinfo',$pile);

        $this->assign('lists',$list);
        $this->assign("show",$show);
        $this->display();
    }
    
    public function detail() {

        /*header*/
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());  // 显示系统当前时间
        $user=mb_substr($_SESSION['admininfo']['username'],0,4).'***';  // 显示系统当前登录的用户名

        /*状态栏*/
        $ob=M('charge_pile');
        $field='*';
        $pile['totalQty']=$ob->field($field)->count(); // 桩总数
        $pile['dcQty']=$ob->where('type=0')->count(); // 直流桩数量
        $pile['acQty']=$ob->where('type=1')->count(); // 交流桩数量
        
        // 今日充电次数
        $times=$ob->field('sum(times_today) as times')->find();
        $pile['times']=$times['times'];
        
        // 注意status值为枚举类型
        $pile['occupyQty']=$ob->where("status='0'")->count(); // 工作中
        $pile['idleQty']=$ob->where("status='1'")->count(); // 空闲
        $pile['faultQty']=$ob->where("status='2'")->count(); // 故障
        
        
        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->assign('pilesinfo',$pile);
        

        
        /*内容 电站信息*/
        $ob=M('charge_station');
        $where['id']=I('get.id'); // 电站ID
        $re=$ob->where($where)->find();
        $re['address']= mb_substr($re['address'],0,30).'*';

        $this->assign('st',$re);
        $this->display();
        
    }
    
}
