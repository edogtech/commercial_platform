<?php
namespace Admin\Controller;
use Think\Controller;
class OperateController extends Controller{
	public function __construct(){
        parent::__construct();
        $msg=session('admininfo');
        $prid=$msg['pridlist'];
        if (!in_array(3,$prid)){
            $this->error('您无此权限！', '../Index/index', 2);
        }
    }
	public function index(){
		$date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());
        // 在header显示系统当前登录的用户名
        $user = strlen($_SESSION['admininfo']['uname']) > 8 ? mb_substr($_SESSION['admininfo']['uname'], 0, 8) . '***' : $_SESSION['admininfo']['uname'];
        $msg=session('admininfo');

        //查询页面内容
        //添加检索条件
        if(!empty(trim($_GET['search']))){
            $map['phone']=array('eq',$_GET['search']);
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

        $map['mid']=array('eq',$_SESSION['admininfo']['identity']);

        //订单数据
        $count=M('workorder_control')->where($map)->count();

        $page= new \Think\Page($count,5);
        $show=$page->show();
        $order=M('workorder_control')->order('id desc')->where($map)->limit($page->firstRow,$page->listRows)->select();

        //待处理数据
        $count=array();
        $workorder=M('workorder_control')->where("mid={$_SESSION['admininfo']['identity']} and status=1")->count();
        $costorder=M('cost_control')->where("mid={$_SESSION['admininfo']['identity']}")->count();
        $priceorder=M('price_control')->where("mid={$_SESSION['admininfo']['identity']}")->count();
        $invoiceorder=M('invoice_control')->where("mid={$_SESSION['admininfo']['identity']} and status=1")->count();


        $count['workorder']=$workorder;
        $count['costorder']=$costorder;
        $count['priceorder']=$priceorder;
        $count['invoiceorder']=$invoiceorder;

        $this->assign('lists',$order);
        $this->assign('show',$show);
        $this->assign('count',$count);
        $this->assign(array('curuser'=>$user,'prid'=>$msg['pridlist'],'curdate'=>$date));
		$this->display();
	}

    public function  work_add(){

        if( empty($_POST['customer']) || empty($_POST['phone']) || empty($_POST['describe']) ||empty($_POST['type'])){
            $this->error('请填写完整信息');
        }
        $data['customer']=trim($_POST['customer']);
        $data['phone']=trim($_POST['phone']);
        $data['describe']=trim($_POST['describe']);
        $data['type']=trim($_POST['type']);
        $data['operator']=trim($_POST['operator']);
        $data['mid']=trim($_POST['mid']);
        $data['addtime']=time();
        $data['status']=1;
        $data['order_number']='EGD'.get_micro_time(3).mt_rand(1000,9999);;
        $data['feedback']='';

        $res=M('workorder_control')->add($data);

        if($res){
            $this->success('添加成功',U('Operate/index'));
        }else{
            $this->error('添加失败');
        }


    }

    public function work_excel(){
        include './Public/phpexcel/Classes/PHPExcel.php';
        include './Public/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
        set_time_limit(0);

        //接收筛选参数
        //添加检索条件
        if(!empty(trim($_GET['search']))){
            $map['phone']=array('eq',$_GET['search']);
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

        $map['mid']=array('eq',$_GET['mid']);

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

    public function cost(){
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());
        // 在header显示系统当前登录的用户名
        $user = strlen($_SESSION['admininfo']['uname']) > 8 ? mb_substr($_SESSION['admininfo']['uname'], 0, 8) . '***' : $_SESSION['admininfo']['uname'];
        $msg=session('admininfo');

        //查询页面内容
        //添加检索条件

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

        $map['mid']=array('eq',$_SESSION['admininfo']['identity']);

        //订单数据
        $count=M('cost_control')->where($map)->count();

        $page= new \Think\Page($count,5);
        $show=$page->show();
        $order=M('cost_control')->order('id desc')->where($map)->limit($page->firstRow,$page->listRows)->select();

        //页头待处理数据
        $count=array();
        $workorder=M('workorder_control')->where("mid={$_SESSION['admininfo']['identity']} and status=1")->count();
        $costorder=M('cost_control')->where("mid={$_SESSION['admininfo']['identity']}")->count();
        $priceorder=M('price_control')->where("mid={$_SESSION['admininfo']['identity']}")->count();
        $invoiceorder=M('invoice_control')->where("mid={$_SESSION['admininfo']['identity']} and status=1")->count();


        $count['workorder']=$workorder;
        $count['costorder']=$costorder;
        $count['priceorder']=$priceorder;
        $count['invoiceorder']=$invoiceorder;

        $this->assign('lists',$order);
        $this->assign('show',$show);
        $this->assign('count',$count);
        $this->assign(array('curuser'=>$user,'prid'=>$msg['pridlist'],'curdate'=>$date));
        $this->display();
    }

    public function cost_excel(){
        include './Public/phpexcel/Classes/PHPExcel.php';
        include './Public/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
        set_time_limit(0);

        //接收筛选参数
        //添加检索条件
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

        $map['mid']=array('eq',$_GET['mid']);

        //先查询数据
        $res=M('cost_control')->where($map)->select();

        if(empty($res)){
            echo '没有数据可导出,请查看您是否有成本管理录入或检查您的筛选条件';
            exit;
        }
        //设置F列(订单号)为文本格式
        $objPHPExcel=new \PHPExcel();//新建一个excel文件类

        //设置表头
        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','流水号');
        $objPHPExcel->getActiveSheet()->setCellValue('C1','费用金额');
        $objPHPExcel->getActiveSheet()->setCellValue('D1','费用类别');
        $objPHPExcel->getActiveSheet()->setCellValue('E1','工单日期');
        $objPHPExcel->getActiveSheet()->setCellValue('F1','操作员');


        //设置每列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $line=2;
        foreach($res as $k=>$v){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$line,$v['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$line,$v['order_number']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$line,$v['price']);;

            switch ($v['type']) {
                case 1:
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$line, '电费缴纳');
                    break;
                case 2:
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$line, '维护费用');
                    break;
                case 3:
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.$line,'其他问题');
                    break;
            }

            $objPHPExcel->getActiveSheet()->setCellValue('E'.$line,date('Y-m-d H:i:s',$v['addtime']));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$line,$v['operator']);

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

    public function  cost_add(){

        if( empty($_POST['type']) || empty($_POST['price']) ){
            $this->error('请填写完整信息');
        }
        $data['type']=trim($_POST['type']);
        $data['price']=trim($_POST['price']);
        $data['operator']=trim($_POST['operator']);
        $data['mid']=trim($_POST['mid']);
        $data['addtime']=time();
        $data['order_number']='ECB'.get_micro_time(3).mt_rand(1000,9999);
        $data['operator_level']=trim($_POST['operator_level']);

        $res=M('cost_control')->add($data);

        if($res){
            $this->success('添加成功',U('Operate/cost'));
        }else{
            $this->error('添加失败');
        }

    }

    public function price(){
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());
        // 在header显示系统当前登录的用户名
        $user = strlen($_SESSION['admininfo']['uname']) > 8 ? mb_substr($_SESSION['admininfo']['uname'], 0, 8) . '***' : $_SESSION['admininfo']['uname'];
        $msg=session('admininfo');

        //查询页面内容
        //添加检索条件

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

        $map['mid']=array('eq',$_SESSION['admininfo']['identity']);

        //订单数据
        $count=M('price_control')->where($map)->count();

        $page= new \Think\Page($count,5);
        $show=$page->show();
        $order=M('price_control')->order('id desc')->where($map)->limit($page->firstRow,$page->listRows)->select();

        //页头待处理数据
        $count=array();
        $workorder=M('workorder_control')->where("mid={$_SESSION['admininfo']['identity']} and status=1")->count();
        $costorder=M('cost_control')->where("mid={$_SESSION['admininfo']['identity']}")->count();
        $priceorder=M('price_control')->where("mid={$_SESSION['admininfo']['identity']}")->count();
        $invoiceorder=M('invoice_control')->where("mid={$_SESSION['admininfo']['identity']} and status=1")->count();


        $count['workorder']=$workorder;
        $count['costorder']=$costorder;
        $count['priceorder']=$priceorder;
        $count['invoiceorder']=$invoiceorder;

        $this->assign('lists',$order);
        $this->assign('show',$show);
        $this->assign('count',$count);
        $this->assign(array('curuser'=>$user,'prid'=>$msg['pridlist'],'curdate'=>$date));
        $this->display();
    }

    public function price_excel(){
        include './Public/phpexcel/Classes/PHPExcel.php';
        include './Public/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
        set_time_limit(0);

        //接收筛选参数
        //添加检索条件
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

        $map['mid']=array('eq',$_GET['mid']);

        //先查询数据
        $res=M('price_control')->where($map)->select();

        if(empty($res)){
            echo '没有数据可导出,请查看您是否有成本管理录入或检查您的筛选条件';
            exit;
        }
        //设置F列(订单号)为文本格式
        $objPHPExcel=new \PHPExcel();//新建一个excel文件类

        //设置表头
        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','流水号');
        $objPHPExcel->getActiveSheet()->setCellValue('C1','费用分类');
        $objPHPExcel->getActiveSheet()->setCellValue('D1','原价格');
        $objPHPExcel->getActiveSheet()->setCellValue('E1','新价格');
        $objPHPExcel->getActiveSheet()->setCellValue('F1','订单日期');
        $objPHPExcel->getActiveSheet()->setCellValue('G1','操作员');


        //设置每列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $line=2;
        foreach($res as $k=>$v){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$line,$v['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$line,$v['order_number']);
            switch ($v['type']) {
                case 1:
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$line, '停车费');
                    break;
                case 2:
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$line, '服务费');
                    break;
                case 3:
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.$line,'充电费');
                    break;
            }

            $objPHPExcel->getActiveSheet()->setCellValue('D'.$line,$v['old_price']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$line,$v['new_price']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$line,date('Y-m-d H:i:s',$v['addtime']));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$line,$v['operator']);

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

    public function invoice(){
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());
        // 在header显示系统当前登录的用户名
        $user = strlen($_SESSION['admininfo']['uname']) > 8 ? mb_substr($_SESSION['admininfo']['uname'], 0, 8) . '***' : $_SESSION['admininfo']['uname'];
        $msg=session('admininfo');

        //查询页面内容
        //添加检索条件
        if(!empty(trim($_GET['search']))){
            $map['phone']=array('eq',$_GET['search']);
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

        $map['mid']=array('eq',$_SESSION['admininfo']['identity']);

        //订单数据
        $count=M('invoice_control')->where($map)->count();

        $page= new \Think\Page($count,5);
        $show=$page->show();
        $order=M('invoice_control')->order('id desc')->where($map)->limit($page->firstRow,$page->listRows)->select();

        //待处理数据
        $count=array();
        $workorder=M('workorder_control')->where("mid={$_SESSION['admininfo']['identity']} and status=1")->count();
        $costorder=M('cost_control')->where("mid={$_SESSION['admininfo']['identity']}")->count();
        $priceorder=M('price_control')->where("mid={$_SESSION['admininfo']['identity']}")->count();
        $invoiceorder=M('invoice_control')->where("mid={$_SESSION['admininfo']['identity']} and status=1")->count();


        $count['workorder']=$workorder;
        $count['costorder']=$costorder;
        $count['priceorder']=$priceorder;
        $count['invoiceorder']=$invoiceorder;

        $this->assign('lists',$order);
        $this->assign('show',$show);
        $this->assign('count',$count);
        $this->assign(array('curuser'=>$user,'prid'=>$msg['pridlist'],'curdate'=>$date));
        $this->display();
    }


    public function invoice_add(){
        if(empty($_POST['customer']) || empty($_POST['phone'])  || empty($_POST['header']) || empty($_POST['price']) || empty($_POST['address']) || empty($_POST['type']) ){
            $this->error('请选择或填写完整参数');
        }

        $data['order_number']='EFP'.get_micro_time(3).mt_rand(1000,9999);
        $data['customer']=trim($_POST['customer']);
        $data['phone']=trim($_POST['phone']);
        $data['header']=trim($_POST['header']);
        $data['price']=trim($_POST['price']);
        $data['address']=trim($_POST['address']);
        $data['type']=trim($_POST['type']);
        $data['mid']=trim($_POST['mid']);
        $data['status']=1;
        $data['operator']=trim($_POST['operator']);
        $data['addtime']=time();

        $res=M('invoice_control')->add($data);
        if($res){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }


    }

    public function invoice_excel(){
        include './Public/phpexcel/Classes/PHPExcel.php';
        include './Public/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
        set_time_limit(0);

        //接收筛选参数
        //添加检索条件
        if(!empty(trim($_GET['search']))){
            $map['phone']=array('eq',$_GET['search']);
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

        $map['mid']=array('eq',$_GET['mid']);

        //先查询数据
        $res=M('invoice_control')->where($map)->select();

        if(empty($res)){
            echo '没有数据可导出,请查看您是否有成本管理录入或检查您的筛选条件';
            exit;
        }
        //设置F列(订单号)为文本格式
        $objPHPExcel=new \PHPExcel();//新建一个excel文件类

        //设置表头
        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','流水号');
        $objPHPExcel->getActiveSheet()->setCellValue('C1','联系人电话');
        $objPHPExcel->getActiveSheet()->setCellValue('D1','联系人姓名');
        $objPHPExcel->getActiveSheet()->setCellValue('E1','发票类型');
        $objPHPExcel->getActiveSheet()->setCellValue('F1','发票价格');
        $objPHPExcel->getActiveSheet()->setCellValue('G1','申请日期');
        $objPHPExcel->getActiveSheet()->setCellValue('H1','操作员');


        //设置每列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $line=2;
        foreach($res as $k=>$v){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$line,$v['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$line,$v['order_number']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$line,'tel:'.$v['phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$line,$v['customer']);
            switch ($v['type']) {
                case 1:
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, '充电发票');
                    break;
            }
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$line,$v['price']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$line,date('Y-m-d H:i:s',$v['addtime']));
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$line,$v['operator']);

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