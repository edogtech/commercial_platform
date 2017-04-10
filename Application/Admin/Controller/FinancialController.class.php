<?php
namespace Admin\Controller;
use Think\Controller;
class FinancialController extends Controller {
    public function __construct(){
        parent::__construct();
        $msg=session('admininfo');
        $prid=$msg['pridlist'];
        if (!in_array(2,$prid)){
            $this->error('您无此权限！');
        }
    }
    //关于我们页面的展示
    public function index(){
        /*header*/
        // 在header显示系统当前时间
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());
        // 在header显示系统当前登录的用户名
        $user=mb_substr($_SESSION['admininfo']['uname'],0,4).'***';
        $msg=session('admininfo');

        //添加检索条件
        if(!empty(trim($_GET['search']))){
            $map['station_name']=array('like','%'.$_GET['search'].'%');
        }

        if(!empty(trim($_GET['time_start'])) && empty(trim($_GET['time_end']))){
            $map['addtime']=array('gt',strtotime($_GET['time_start']));
        }

        if(empty(trim($_GET['time_start'])) && !empty(trim($_GET['time_end']))){
            $map['addtime']=array('lt',strtotime($_GET['time_start']));
        }

        if(!empty(trim($_GET['time_start'])) && !empty(trim($_GET['time_end']))){
            $starttime=strtotime($_GET['time_start']);
            $endtime=strtotime($_GET['time_end']);
            $map['addtime']=array('exp',"between $starttime and $endtime");
        }


        //订单数据
        $count=M('charge_order')->where($map)->count();

        $page= new \Think\Page($count,5);
        $show=$page->show();
        $order=M('charge_order')->order('id desc')->where($map)->limit($page->firstRow,$page->listRows)->select();

        $month_now=date('Ym',time());
        //统计数据
        $month_order=M()->query("select count(*) as count from charge_order where FROM_UNIXTIME(addtime,'%Y%m')=".$month_now." and user_id=".$_SESSION['admininfo']['uid']);
        $month_sum=M()->query("select SUM(charge_price) as sum from charge_order where FROM_UNIXTIME(addtime,'%Y%m')=".$month_now." and user_id=".$_SESSION['admininfo']['uid']);

        $totle['order']=$month_order[0]['count'];
        $totle['sum']=$month_sum[0]['sum'];
        $this->assign('curuser',$user);
        $this->assign('prid',$msg['pridlist']);
        $this->assign('curdate',$date);
        $this->assign('show',$show);
        $this->assign('lists',$order);
        $this->assign('totle',$totle);
        $this->display();
    }

    public function excel(){
        include './Public/phpexcel/Classes/PHPExcel.php';
        include './Public/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
        set_time_limit(0);


        //先查询数据

        $res=M('charge_order')->select();
        if(empty($res)){
            echo 'fail';
            exit;
        }
        //设置F列(订单号)为文本格式
        $objPHPExcel=new \PHPExcel();//新建一个excel文件类

        //设置表头
        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','订单号');
        $objPHPExcel->getActiveSheet()->setCellValue('C1','充电站名称');
        $objPHPExcel->getActiveSheet()->setCellValue('D1','用户电话');
        $objPHPExcel->getActiveSheet()->setCellValue('E1','桩号');
        $objPHPExcel->getActiveSheet()->setCellValue('F1','充电量');
        $objPHPExcel->getActiveSheet()->setCellValue('G1','充电金额');
        $objPHPExcel->getActiveSheet()->setCellValue('H1','支付方式');
        $objPHPExcel->getActiveSheet()->setCellValue('I1','订单日期');

        //设置每列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(22);
        $line=2;
        foreach($res as $k=>$v){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$line,$v['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$line,$v['order_number']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$line,$v['station_name']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$line,'tel:'.$v['phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$line,'num: '.$v['pile_num']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$line,$v['quantity']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$line,$v['charge_price']);
            switch ($v['payment_way'])
            {
                case 1:
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$line,'充点卡');
                    break;
                case 2:
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$line,'支付宝');
                    break;
                case 3:
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$line,'微信');
                    break;

            }
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$line,date('Y-m-d H:i:s',$v['addtime']));
            $line++;
        }

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $today=date('Y-m-d',time());
        if(!file_exists("./Public/saveExcel/{$today}")){
            mkdir("./Public/saveExcel/{$today}");
        }
        if(!file_exists("./Public/saveExcel")){
            mkdir("./Public/saveExcel");
        }
        $path="./Public/saveExcel/{$today}/{$_SESSION['admininfo']['uname']}".time().".xlsx";

        $resaa=$objWriter->save($path);


    }
    
}
