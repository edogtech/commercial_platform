<?php
namespace Admin\Controller;
use Think\Controller;
class OperateController extends Controller{
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

        //查询页面内容
        //添加检索条件
        if(!empty(trim($_GET['search']))){
            $map['phone']=array('like','%'.$_GET['search'].'%');
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
        $count=M('workorder_control')->where($map)->count();

        $page= new \Think\Page($count,5);
        $show=$page->show();
        $order=M('workorder_control')->order('id desc')->where($map)->limit($page->firstRow,$page->listRows)->select();





        $this->assign('lists',$order);
        $this->assign('show',$show);
        $this->assign(array('curuser'=>$user,'prid'=>$msg['pridlist'],'curdate'=>$date));
		$this->display();
	}

    public function excel(){
        include './Public/phpexcel/Classes/PHPExcel.php';
        include './Public/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
        set_time_limit(0);

        //接收筛选参数
        //添加检索条件
        if(!empty(trim($_GET['search']))){
            $map['phone']=array('like','%'.$_GET['search'].'%');
        }

        if(!empty(trim($_GET['start'])) && empty(trim($_GET['end']))){
            $map['addtime']=array('gt',strtotime($_GET['start']));
        }

        if(empty(trim($_GET['start'])) && !empty(trim($_GET['end']))){
            $map['addtime']=array('lt',strtotime($_GET['end']));
        }

        if(!empty(trim($_GET['start'])) && !empty(trim($_GET['end']))){
            $starttime=strtotime($_GET['start']);
            $endtime=strtotime($_GET['end']);
            $map['addtime']=array('exp',"between $starttime and $endtime");
        }



        //先查询数据

        $res=M('workorder_control')->where($map)->select();

        if(empty($res)){
            echo '没有数据可导出,请查看您是否有工单或检查您的筛选条件';
            exit;
        }
        //设置F列(订单号)为文本格式
        $objPHPExcel=new \PHPExcel();//新建一个excel文件类

        //设置表头
        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','流水号');
        $objPHPExcel->getActiveSheet()->setCellValue('C1','用户电话');
        $objPHPExcel->getActiveSheet()->setCellValue('D1','用户称呼');
        $objPHPExcel->getActiveSheet()->setCellValue('E1','问题类型');
        $objPHPExcel->getActiveSheet()->setCellValue('F1','工单日期');
        $objPHPExcel->getActiveSheet()->setCellValue('G1','操作员');
        $objPHPExcel->getActiveSheet()->setCellValue('H1','状态');

        //设置每列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $line=2;
        foreach($res as $k=>$v){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$line,$v['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$line,$v['order_number']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$line,'tel:'.$v['phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$line,$v['customer']);

            switch ($v['type']) {
                case 1:
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, 'app异常');
                     break;
                case 2:
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, '电桩异常');
                    break;
                case 3:
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line,'刷卡异常');
                    break;
                case 4:
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, '其他问题');
                    break;
            }

            $objPHPExcel->getActiveSheet()->setCellValue('F'.$line,date('Y-m-d H:i:s',$v['addtime']));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$line,$v['operator']);
            switch ($v['status'])
            {
                case 1:
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$line,'待处理');
                    break;
                case 2:
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.$line,'已解决');
                    break;

            }

            $line++;
        }

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);

        $excelName='统计表';
        header ( "Content-Type: application/force-download" );
        header ( "Content-Type: application/octet-stream" );
        header ( "Content-Type: application/download" );
        header ( "Content-Disposition: attachment; filename=" . $excelName . ".xlsx" );
        header ( "Content-Transfer-Encoding: binary" );
        header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header ( "Pragma: no-cache" );

        $objWriter->save('php://output');


    }

}