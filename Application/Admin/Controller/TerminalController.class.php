<?php
namespace Admin\Controller;
use Think\Controller;

class TerminalController extends Controller {
    
    private $term; // 条件 商户ID
    private $ob_pile; // 桩表
    private $ob_station; // 站表
    
    public function __construct(){
        parent::__construct();
        $msg=session('admininfo');
        $prid=$msg['pridlist'];
        $ck = cookie('identity:');
        if (!in_array(1, $prid) || empty($ck)) {
            $this->redirect('Index/index');
        }

        $this->term[user_id]=$msg['identity'];
        $this->ob_pile=M('charge_pile');
        $this->ob_station=M('charge_station');
    }
    //关于我们页面的展示
    public function index(){
        
/*header*/        
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time()); // 显示系统当前时间
        $user = mb_strlen($_SESSION['admininfo']['uname']) > 9 ? mb_substr($_SESSION['admininfo']['uname'], 0, 6) . '***' : $_SESSION['admininfo']['uname']; // 显示系统当前登录的用户名
        $msg=session('admininfo');
        $com = D('user_merchant')->field('company')->where(array('uid' => $msg['identity']))->find();
        $company = $com['company'];
        
/*状态栏*/
//         $ob=M('charge_pile');
        $queryString=$this->ob_pile->join('as p left join charge_station as s on p.station_id=s.id')
                  ->field("count(type) as num,type,sum(s.times_today) as times")
                  ->where($this->term)
                  ->group(type)
                  ->select();

        foreach ($queryString as $k=>$v){
            $pile['totalQty']+=$v['num']; // 桩总数
            $v['type']=='0'?$pile['acQty']=$v['num']:$pile['dcQty']=$v['num']; // 直流交流桩数量
        } 
        
        // 今日充电次数
//         $ob_station=M('charge_station');
        $times=$this->ob_station->field('sum(times_today) as times')->where($this->term)->find();
        $pile['times']=$times['times'];
       
        // 电桩状态
        $queryString=$this->ob_pile->join('as p left join charge_station as s on p.station_id=s.id')
        ->field("count(status) as num,status")
        ->where($this->term)
        ->group(status)
        ->select();

        foreach ($queryString as $k=>$v){
            switch ($v['status']) {
                case '0':
                    $pile['occupyQty']=$v['num']; // 工作中
                    break;
                case '1':
                    $pile['idleQty']=$v['num']; // 空闲
                    break;
                case '2':
                    $pile['faultQty']=$v['num']; // 故障
                    break;                        
            }
        }


        /*列表及分页*/
        $station=I('post.txtStation','','trim');
        $where=array();
        
        if(!empty($station)){
            $where['name']=array('like',"%{$station}%");
        }
        
//         $ob_station=M('charge_station');
        $field='id,name,ac_num+dc_num as pile_total,electricity_history,times_today';
        $where=array_merge($where,$this->term);
        
        $count=$this->ob_station->where($where)->count(); // 取得总条数
        $page= new \Think\Page($count,8); // 根据总条数实例化page类
        $show= $page->show(); // 分页显示输出
        $list = $this->ob_station->field($field)->where($where)->limit($page->firstRow,$page->listRows)->select();
       
        // 统计每电站中桩三种状态的数量
//         $ob_pile=M('charge_pile');
        foreach ($list as $k=>$v) {
            $stationID=$list[$k]['id'];
            $list[$k]['sequence']=$k+1; // 列表序号
            $list[$k]['pile_occupy']=$this->ob_pile->where("station_id=$stationID AND status='0'")->count(); //status为枚举值
            $list[$k]['pile_idle']=$this->ob_pile->where("station_id=$stationID AND status='1'")->count();
            $list[$k]['pile_fault']=$this->ob_pile->where("station_id=$stationID AND status='2'")->count();
        }

        $this->assign('prid',$msg['pridlist']);
        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->assign(array('pilesinfo' => $pile, 'company' => $company));
        $this->assign('lists',$list);
        $this->assign("show",$show);
        $this->display();
    }
    
    public function detail() {

/*header*/
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());  // 显示系统当前时间
        $user = mb_strlen($_SESSION['admininfo']['uname']) > 9 ? mb_substr($_SESSION['admininfo']['uname'], 0, 6) . '***' : $_SESSION['admininfo']['uname'];
        $msg=session('admininfo');
        $com = D('user_merchant')->field('company')->where(array('uid' => $msg['identity']))->find();
        $company = $com['company'];
        
/*状态栏*/
//         $ob=M('charge_pile');
        $queryString=$this->ob_pile->join('as p left join charge_station as s on p.station_id=s.id')
                  ->field("count(type) as num,type,sum(s.times_today) as times")
                  ->where($this->term)
                  ->group(type)
                  ->select();

        foreach ($queryString as $k=>$v){
            $pile['totalQty']+=$v['num']; // 桩总数
            $v['type']=='0'?$pile['acQty']=$v['num']:$pile['dcQty']=$v['num']; // 直流交流桩数量
        }
        
        // 今日充电次数
//         $ob_station=M('charge_station');
        $times=$this->ob_station->field('sum(times_today) as times')->where($this->term)->find();
        $pile['times']=$times['times'];
        
        // 电桩状态
        $queryString=$this->ob_pile->join('as p left join charge_station as s on p.station_id=s.id')
        ->field("count(status) as num,status")
        ->where($this->term)
        ->group(status)
        ->select();

        foreach ($queryString as $k=>$v){
            switch ($v['status']) {
                case '0':
                    $pile['occupyQty']=$v['num']; // 工作中
                    break;
                case '1':
                    $pile['idleQty']=$v['num']; // 空闲
                    break;
                case '2':
                    $pile['faultQty']=$v['num']; // 故障
                    break;                        
            }
        }
        
        $this->assign('prid',$msg['pridlist']);
        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->assign('pilesinfo',$pile);
        
        /*内容 电站信息*/
//         $ob=M('charge_station');
        $where['id']=I('get.id'); // 电站ID
        $re=$this->ob_station->where($where)->find();
        $re['address']= mb_strlen($_SESSION['admininfo']['uname']) > 20 ? mb_substr($re['address'],0,20).'*':mb_substr($re['address'],0,20); // 电站地址
        $re['total']= $re['ac_num']+$re['dc_num']; // 电桩总数
        
        // 当前时段充电费、服务费、停车费用
        $clock=date('H',time());
        $arrayChg=array_filter(explode(',', $re['charging_fee']));
        if (@empty($arrayChg)) {
            $re['charging_fee'] = '该时间段未设定';
        }else{
            for($i=0;$i<sizeof($arrayChg);$i=$i+3){
                $re['charging_fee']='0'; // 如果当前时间不在设定的时间段内
                if($clock>=$arrayChg[$i] && $clock<=$arrayChg[$i+1]){
                    $re['charging_fee'] = $arrayChg[$i + 2] . '元/度';
                } else {
                    $re['charging_fee'] = '该时间段未设定';
                }
            }
        }
        
        $arrayPark=array_filter(explode(',', $re['parking_fee']));
        if(@empty($arrayPark)){
            $re['parking_fee'] = '该时间段未设定';
        }else{
            for($i=0;$i<sizeof($arrayPark);$i=$i+3){
                $re['parking_fee']='0';
                if($clock>=$arrayPark[$i] && $clock<=$arrayPark[$i+1]){
                    $re['parking_fee'] = $arrayPark[$i + 2] . '元/小时';
                } else {
                    $re['parking_fee'] = '该时间段未设定';
                }
            }
        }
        
        $arrayServe=array_filter(explode(',', $re['serving_fee']));
        if(@empty($arrayServe)){
            $re['serving_fee'] = '该时间段未设定';
        }else{
            for($i=0;$i<sizeof($arrayServe);$i=$i+3){
                $re['serving_fee']='0';
                if($clock>=$arrayServe[$i] && $clock<=$arrayServe[$i+1]){
                    $re['serving_fee'] = $arrayServe[$i + 2] . '元/度';
                } else {
                    $re['serving_fee'] = '该时间段未设定';
                }
            }
        }
        

        // 该电站中桩状态
//         $ob_pile=M('charge_pile');
        $stationID=I('get.id');
        
        $pileStatus['occupy']=$this->ob_pile->where("station_id=$stationID AND status='0'")->count();
        $pileStatus['idle']=$this->ob_pile->where("station_id=$stationID AND status='1'")->count();
        $pileStatus['fault']=$this->ob_pile->where("station_id=$stationID AND status='2'")->count();

        // 该电站所有桩数据
        $field='id,station_id,pile_no,type,voltage,current,capacity,status,cur_voltage,cur_current,cur_electricity,cur_price,cur_duration';
        $count=$this->ob_pile->where("station_id=$stationID")->count();
        $page= new \Think\Page($count,7);
        $show= $page->show();
        $pileInfo = $this->ob_pile->field($field)->where("station_id=$stationID")->order('pile_no')->limit($page->firstRow,$page->listRows)->select();

        $this->assign('st',$re); // 站信息
        $this->assign('pilestatus',$pileStatus); // 桩状态
        $this->assign(array('lists' => $pileInfo, 'company' => $company)); // 桩信息
        $this->assign("show",$show);
        $this->display();
        
    }
    
    /*
     * AJAX回调显示充电/服务/停车费
     */
    public function displayFee()
    {
        $stationID = I('post.stationID');
        $type = I('post.type');

        $map['id'] = $stationID;
        $re = $this->ob_station->field('parking_fee,serving_fee,charging_fee')->where($map)->find();

        switch ($type) {
            case 'parking':
                $return = explode(',', $re['parking_fee']);
                @empty(array_slice($return,0,3))?'':$arr[]=array_slice($return,0,3);
                @empty(array_slice($return,3,3))?'':$arr[]=array_slice($return,3,3);
                @empty(array_slice($return,6,6))?'':$arr[]=array_slice($return,6,3);
                break;
            case 'serving':
                $return = explode(',', $re['serving_fee']);
                @empty(array_slice($return,0,3))?'':$arr[]=array_slice($return,0,3);
                @empty(array_slice($return,3,3))?'':$arr[]=array_slice($return,3,3);
                @empty(array_slice($return,6,6))?'':$arr[]=array_slice($return,6,3);
                break;
            case 'charging':
                $return = explode(',', $re['charging_fee']);
                @empty(array_slice($return,0,3))?'':$arr[]=array_slice($return,0,3);
                @empty(array_slice($return,3,3))?'':$arr[]=array_slice($return,3,3);
                @empty(array_slice($return,6,6))?'':$arr[]=array_slice($return,6,3);
                break;
        }

        echo json_encode($arr);
    }

    /*
     * 调整充电/服务/停车费
     */
    public function adjustFee(){
        
        // 初始化循环变量
        $i=0;
        
        // $_POST包含三个时段与价格，最后两个元素分别是电桩ID和调价种类
        foreach ($_POST as $key=>$val) {
            if($val!==0){
                $arrayDuration[$i]=$val;
                $i++;
            }
        }

        $category=array_pop($arrayDuration); // 弹出调整费用类型
        $stationID=array_pop($arrayDuration); // 弹出站ID
        $strDuration=(implode(',',$arrayDuration)); // 生成以逗号分隔的时段与价格字符串
        
        $where['id']=$stationID;
        $res = $this->ob_station->field('name,parking_fee,serving_fee,charging_fee')->where($where)->find();
        
        switch ($category) {
            case 'parking':
                $data['parking_fee']=$strDuration;
                $type = '1';
                $prePrice = $res['parking_fee'];
                break;
            case 'serving':
                $data['serving_fee']=$strDuration;
                $type = '2';
                $prePrice = $res['serving_fee'];
                break;
            case 'charging':
                $data['charging_fee']=$strDuration;
                $type = '3';
                $prePrice = $res['charging_fee'];
                break;
        }
        // 更新电站调价信息
        $re=$this->ob_station->where($where)->data($data)->save();

        // 向价格管理添加记录
        $info['station_name'] = $res['name'];
        $info['station_id'] = $stationID;
        $info['order_number'] = 'EJG' . get_micro_time(3) . mt_rand(1000, 9999);
        $info['type'] = $type;
        $info['operator'] = session('admininfo.uname');
        $info['addtime'] = time();
        $info['old_price'] = $prePrice;
        $info['new_price'] = $strDuration;
        $info['mid'] = session('admininfo.identity');

        $res = M('price_control')->data($info)->add();

        if (empty($re) || empty($res)) {
            //$this->success('修改成功！','index');
            //$this->redirect("Terminal/detail");
            $this->error("修改失败");
        } else {
            $this->success('修改成功！');
        }
        
    }
    
    
    /*
     * crontab根据时段发送调整充电费命令
     */
    public function adjustChargingFeeCommand(){

        // 筛选出更改过电价的电站ID以及调整电价的时间和费用
//         $obStation=M('charge_station');
        $field='id,number,charging_fee,charging_fee_flag';
        $where['charging_fee_flag']='1';
        $reStation=$this->ob_station->field($field)->where($where)->select(); 
        
        if(empty($reStation)){
            exit(0);
        }else {
            $obPile=M('charge_pile');
            
            foreach ($reStation as $k=>$v) {
                $stationID=$reStation[$k]['id']; // 电站ID
            
                // 筛选该电站下所有桩
                $field='pile_no';
                $condition['station_id']=$stationID;
                $rePile=$obPile->field($field)->where($condition)->select();
         
                // 取出电站调价起始时间及价格
                $strArray=explode(',',$reStation[$k]['charging_fee']);
                $j=0;
                for($i=0;$i<count($strArray);$i=$i+3){
                    if(!empty($strArray[$i])){
                        $timeVal[$j]=$strArray[$i]; // 电价起始时间
                        $priceVal[$j]=$strArray[$i+2]; // 电价
                        $j++;
                    }
                }
            
                for($k=0;$k<=count($timeVal);$k++){
                    if ($timeVal[$k]==date('H',time())) {
                        
                        // 调价标志位清0
                        $data['charging_fee_flag']='0';
                        $this->ob_station->where("id=$stationID")->save($data);

                        // 发送调价命令
                        foreach ($rePile as $key=>$value){
                            $pileNo=$rePile[$key]['pile_no'];
                            $answer = modify_pile_price($pileNo,$priceVal[$k]);
                            //print_r($answer);
                            //sleep(30); // 间隔30s
                        }
                    }
            
                }
            
                // 清空数组元素
                unset($timeVal,$priceVal);
            }
        }
        
    }
    
    /*
     * 电桩启停、锁定及重启操作
     */    
    public function controlAction(){
        
        $pileID=I('post.pileID'); // 桩编号
        $actionStr=I('post.actionStr'); // 操作类型
        $userID=I('post.userID'); //用户ID
        $gun=I('post.gun','1','trim'); // 默认为1号枪
        
        switch ($actionStr) {
            case 'open':
                $type='0'; // 开启电桩
                
//                 // for dubug
// //                 sleep(3); 
// //                 $answer['status']='0';
                
                $answer=switch_pile($pileID, $gun, $type,$userID);
                $answer['status']=='0'?$return='1':$return='2'; // 开启充电成功/失败
                
                break;
            case 'close':
                $type='1'; // 关闭电桩
                $answer=switch_pile($pileID, $gun, $type,$userID);
                $answer['status']=='0'?$return='1':$return='2'; // 关闭充电成功/失败
                break;
            case 'reset':
                $answer=reset_pile($pileID);
                $answer['status']=='0'?$return='1':$return='2'; // 电桩重启成功/失败
                break;
            case 'lock':
                $type='0'; // 0锁定 1解锁
                $answer=lock_pile($pileID, $gun, $type);
                $answer['status']=='0'?$return='1':$return='2'; // 电桩重启成功/失败
                break;

        }
        echo $return; // 返回电桩控制操作结果
        
        // for dubug
//         echo("电桩ID $pileID 操作是 $actionStr 用户ID $userID");
    }

    
}
