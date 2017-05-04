<?php
namespace Admin\Controller;
use Think\Controller;

class MaintenanceController extends Controller {
    private $condition; // 条件 工单商户ID
    private $term; // 条件 商户ID
    private $ob_workorder; // 工单表
    private $ob_station; // 站表
    
    public function __construct(){
        parent::__construct();
       $msg=session('admininfo');
       $prid=$msg['pridlist'];
        $ck = cookie('identity:');
        if (!in_array(4, $prid) || empty($ck)) {
            $this->redirect('Index/index');
        }

        $this->term['user_id']= $msg['identity'];
        $this->condition['mid']= $msg['identity'];
        
        $this->ob_workorder=M('workorder_control');
        $this->ob_station=M('charge_station');
        
        $this->ob_tmp=M('charge_tmp_eledata');
        $this->ob_tmp_err=M('charge_tmp_eledata_err');
    }

    /*
     * 故障显示
     * */
    public function index(){
        /*header*/
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time()); // 显示系统当前时间 

        $user = strlen($_SESSION['admininfo']['uname']) > 8 ? mb_substr($_SESSION['admininfo']['uname'], 0, 8) . '***' : $_SESSION['admininfo']['uname'];// 显示系统当前登录的用户名
        $msg=session('admininfo');
        
        /*状态栏*/
        // 工单（总数、待处理、已处理）
        $today=date('Ymd',time());
        $string="date('Ymd',backtime)";
        $query="SELECT count(1) AS total,(SELECT count(*)FROM workorder_control WHERE STATUS = 1) AS pending,	(SELECT	count(*) FROM	workorder_control	WHERE	STATUS = 2 AND DATE_FORMAT(NOW(),'%Y%m%d') = $today) AS processed FROM workorder_control where status=2";
        $sheetNum=$this->ob_workorder->query($query);
        
        
        /*
         * 故障（总数、待处理、已处理）逻辑
         * 待处理：取err异常表订单数据，根据batch_no检索数据表eledata中是否存在batch_status为3的交易记录，不存在即为待处理异常订单
         * 已处理：如果存在batch_status为3，则此异常订单已解决
         * */

        $field='batch_no';
        $errSet=$this->ob_tmp_err->field($field)->select();
        foreach ($errSet as $k=>$v){
            $map['batch_no']=$v['batch_no'];
            $tmpSet=$this->ob_tmp->where($map)->order('id desc')->find(); // 取最新交易记录
            if($tmpSet['batch_status']=='3'){
                date('Ymd',$tmpSet['ele_endtime'])!=$today?:$failureNum['processed']++; // 今日处理故障数增1
                $failureNum['total']++; // 历史处理故障总数增1

            }else{
                $failureNum['pending']++; // 未处理故障数量增1
            }
        }
        
        /*列表及分页*/
        $where=array(); // 设定搜索条件
        
        $where['tmp.batch_no']='ffffffff';
        $station=I('get.search','','trim');
        $startTime=strtotime(I('get.time_start','','trim'));
        $endTime=strtotime(I('get.time_end','','trim'));
        
        if(!empty($station)){
            $where['name']=array('like',"%{$station}%");
        }
        
        if (empty($startTime) && !empty($endTime)) {
            $where['err.ele_startTime']=array('elt',$endTime);
        }elseif (!empty($startTime) && empty($endTime)){
            $where['err.ele_startTime']=array('egt',$startTime);
        }elseif (!empty($startTime) && !empty($endTime)){
            $where['err.ele_startTime']=array('between',"$startTime,$endTime");
        }
        
        $where=array_merge($where,$this->term);
        
        // 取得总条数
        $count=$this->ob_tmp_err->join('as err left join charge_tmp_eledata as tmp on tmp.pile_no=err.pile_no left join charge_station as st on st.number=tmp.site_no')
                    ->field("err.batch_no,st.name,err.pile_no,err.gun_fault,err.ele_startTime")
                    ->where($where)
                    ->count();
        
        // 实例化page类分页显示输出
        $page= new \Think\Page($count,8);
        $show= $page->show();
        $list=$this->ob_tmp_err->join('as err left join charge_tmp_eledata as tmp on tmp.pile_no=err.pile_no left join charge_station as st on st.number=tmp.site_no')
                   ->field("err.batch_no,st.name,err.pile_no,err.gun_fault,err.ele_startTime")
                   ->where($where)
                   ->limit($page->firstRow,$page->listRows)
                   ->select();
   
        // 处理列表数据
        unset($map);
        foreach ($list as $k=>$v) {
            $list[$k]['sequence']=$k+1; // 列表序号
            
            $stringPileNo=substr($list[$k]['pile_no'],-3);
            settype($stringPileNo,"integer");
            $list[$k]['pile_no']=$stringPileNo.'号桩';
            
            $map['batch_no']=$v['batch_no'];
            $tmpSet=$this->ob_tmp->where($map)->order('id desc')->find(); // 取最新交易记录
            $tmpSet['batch_status']=='3'?$list[$k]['status']=2:$list[$k]['status']=1; //1待处理 2已解决
        }

        
        // 按状态待处理-已解决排序
        for ($i = 0; $i < count($list)-1; $i++) {
            for ($j = $i+1; $j < count($list); $j++) {
                if($list[$i]['status']>$list[$j]['status']){
                    $tmp=$list[$i]['status'];
                    $list[$i]['status']=$list[$j]['status'];
                    $list[$j]['status']=$tmp;
                }
            }
        }
        
        $this->assign('prid',$msg['pridlist']);

        $this->assign('sheetNum',$sheetNum);
        $this->assign('failureNum',$failureNum);
        $this->assign('lists',$list);
        $this->assign("show",$show);
        $this->assign(array('curuser'=>$user,'prid'=>$msg['pridlist'],'curdate'=>$date));
        $this->display();
    }
    
    
    /*
     * 工单处理
     * 
    */
    public function worksheet(){
        
/*header*/
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time()); // 显示系统当前时间

        $user = strlen($_SESSION['admininfo']['uname']) > 8 ? mb_substr($_SESSION['admininfo']['uname'], 0, 8) . '***' : $_SESSION['admininfo']['uname']; // 显示系统当前登录的用户名
        $msg=session('admininfo');
        
        /*状态栏*/
		// 工单（待处理、已处理、总数）
		$today=date('Ymd',time());
		$string="date('Ymd',backtime)";
        $query="SELECT count(1) AS total,(SELECT count(*)FROM workorder_control WHERE STATUS = 1) AS pending,	(SELECT	count(*) FROM	workorder_control	WHERE	STATUS = 2 AND DATE_FORMAT(NOW(),'%Y%m%d') = $today) AS processed FROM workorder_control where status=2";
        $sheetNum=$this->ob_workorder->query($query);
        
        /*
         * 故障（总数、待处理、已处理）逻辑
         * 待处理：取err异常表订单数据，根据batch_no检索数据表eledata中是否存在batch_status为3的交易记录，不存在即为待处理异常订单
         * 已处理：如果存在batch_status为3，则此异常订单已解决
         * */
        $field='batch_no';
        $errSet=$this->ob_tmp_err->field($field)->select();
        foreach ($errSet as $k=>$v){
            $map['batch_no']=$v['batch_no'];
            $tmpSet=$this->ob_tmp->where($map)->order('id desc')->find(); // 取最新交易记录
            if($tmpSet['batch_status']=='3'){
                date('Ymd',$tmpSet['ele_endtime'])!=$today?:$failureNum['processed']++; // 今日处理故障数增1
                $failureNum['total']++; // 历史处理故障总数增1
        
            }else{
                $failureNum['pending']++; // 未处理故障数量增1
            }
        }
        
        /*列表及分页*/
        $where=array();
        
        $phone=I('get.search','','trim');
        $startTime=strtotime(I('get.time_start','','trim'));
        $endTime=strtotime(I('get.time_end','','trim'));
        
        if(!empty($phone)){
            $where['phone']=array('like',"%{$phone}%");
        }

        if (empty($startTime) && !empty($endTime)) {
            $where['addtime']=array('elt',$endTime);
        }elseif (!empty($startTime) && empty($endTime)){
            $where['addtime']=array('egt',$startTime);
        }elseif (!empty($startTime) && !empty($endTime)){
            $where['addtime']=array('between',"$startTime,$endTime");
        }

        $where=array_merge($where,$this->condition);

        $count=$this->ob_workorder->field($field)->where($where)->count();
        $page= new \Think\Page($count,8); 
        $show= $page->show();
        
        $field='id,order_number,customer,operator,phone,type,describe,addtime,telephone,status';
        $list=$this->ob_workorder->field($field)->where($where)->order('status')->limit($page->firstRow,$page->listRows)->select();

        // 处理列表数据
        foreach ($list as $k=>$v) {
            $list[$k]['sequence']=$k+1; // 列表序号
        }
        $this->assign(array('curuser'=>$user,'prid'=>$msg['pridlist'],'curdate'=>$date));
        $this->assign('sheetNum',$sheetNum);
        $this->assign('failureNum',$failureNum);
        $this->assign('lists',$list);
        $this->assign("show",$show);
        $this->display();
    }
    
    /*
     * 处理工单批复信息
     * 
     * */
    public function processSheet() {
        $sheetID=I('post.sheetID'); // 工单ID
        $response=I('post.response','','trim'); // 处理信息
        
        $ob=M('workorder_control');
        
        $data['feedback']=$response;
        $data['status']='2';
        $data['backtime']=time();
        
        $where['id']=$sheetID;
        
        $re=$ob->where($where)->save($data);
        if(!empty($re)){
            $this->success("工单处理完毕",U("Maintenance/index"));
        }else {
            $this->error("工单处理有误");
        }

//         echo 'sheetID:'.$sheetID; // for debug
        
    }
    
    // 导出工单信息
    public function exportWorksheet(){
        $msg=session('admininfo');
        
        include './Public/phpexcel/Classes/PHPExcel.php';
        include './Public/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
        set_time_limit(0);
    
        //接收筛选参数
        //添加检索条件
        $phone=I('get.search','','trim');
        $startTime=strtotime(I('get.time_start','','trim'));
        $endTime=strtotime(I('get.time_end','','trim'));
        
        if(!empty($phone)){
            $where['phone']=array('like',"%{$phone}%");
        }
        
        if (empty($startTime) && !empty($endTime)) {
            $where['w.addtime']=array('elt',$endTime);
        }elseif (!empty($startTime) && empty($endTime)){
            $where['w.addtime']=array('egt',$startTime);
        }elseif (!empty($startTime) && !empty($endTime)){
            $where['w.addtime']=array('between',"$startTime,$endTime");
        }
        
       $where=array_merge($where,$this->condition);

        // 查询数据
       $field='id,order_number,customer,operator,phone,type,describe,addtime,telephone,status';
       $res=$this->ob_workorder->field($field)->where($where)->order('status')->select();

       if(empty($res)){
            echo 'fail';
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
        $objPHPExcel->getActiveSheet()->setCellValue('F1','订单日期');
        $objPHPExcel->getActiveSheet()->setCellValue('G1','操作员');
        $objPHPExcel->getActiveSheet()->setCellValue('H1','状态');
    
        //设置每列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        $line=2;
        foreach($res as $k=>$v){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$line,$k+1);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$line,$v['order_number']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$line,$v['phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$line,$v['customer']);
            switch ($v['type'])
            {
                case 1:
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line,'APP异常');
                    break;
                case 2:
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line,'电桩异常');
                    break;
                case 3:
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line,'刷卡异常');
                    break;
                case 4:
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line,'其他问题');
                    break;                    
            }
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$line,$v['addtime']);
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
  
    // 导出故障信息
    public function exportFailure(){
        
        include './Public/phpexcel/Classes/PHPExcel.php';
        include './Public/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
        set_time_limit(0);
        
        $where['tmp.batch_no']='ffffffff';
        $station=I('get.search','','trim');
        $startTime=strtotime(I('get.time_start','','trim'));
        $endTime=strtotime(I('get.time_end','','trim'));
        
        if(!empty($station)){
            $where['name']=array('like',"%{$station}%");
        }
        
        if (empty($startTime) && !empty($endTime)) {
            $where['err.ele_startTime']=array('elt',$endTime);
        }elseif (!empty($startTime) && empty($endTime)){
            $where['err.ele_startTime']=array('egt',$startTime);
        }elseif (!empty($startTime) && !empty($endTime)){
            $where['err.ele_startTime']=array('between',"$startTime,$endTime");
        }
        
        $where=array_merge($where,$this->term);
        
        $res=$this->ob_tmp_err->join('as err left join charge_tmp_eledata as tmp on tmp.pile_no=err.pile_no left join charge_station as st on st.number=tmp.site_no')
        ->field("err.batch_no,st.name,err.pile_no,err.gun_fault,err.ele_startTime")
        ->where($where)
        ->select();
         
        // 处理列表数据
        foreach ($res as $k=>$v) {
            $res[$k]['sequence']=$k+1; // 列表序号
            
            $stringPileNo=substr($res[$k]['pile_no'],-3);
            settype($stringPileNo,"integer");
            $res[$k]['pile_no']=$stringPileNo.'号桩';
            
            $map['batch_no']=$v['batch_no'];
            $tmpSet=$this->ob_tmp->where($map)->order('id desc')->find(); // 取最新交易记录
            $tmpSet['batch_status']=='3'?$res[$k]['status']=2:$res[$k]['status']=1; //1待处理 2已解决
        }
        
        if(empty($res)){
            echo 'fail';
            exit;
        }
        //设置F列(订单号)为文本格式
        $objPHPExcel=new \PHPExcel();//新建一个excel文件类
        
        //设置表头
        $objPHPExcel->getActiveSheet()->setCellValue('A1','序号');
        $objPHPExcel->getActiveSheet()->setCellValue('B1','流水号');
        $objPHPExcel->getActiveSheet()->setCellValue('C1','充电站名称');
        $objPHPExcel->getActiveSheet()->setCellValue('D1','桩号');
        $objPHPExcel->getActiveSheet()->setCellValue('E1','故障代码');
        $objPHPExcel->getActiveSheet()->setCellValue('F1','订单日期');
        $objPHPExcel->getActiveSheet()->setCellValue('G1','售后电话');
        $objPHPExcel->getActiveSheet()->setCellValue('H1','状态');
        
        //设置每列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
        
        $line=2;
        foreach($res as $k=>$v){
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$line,$k+1);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$line,$v['batch_no']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$line,$v['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$line,$v['pile_no']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$line,$v['gun_fault']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$line,date('Y-m-d H:i:s',$v['ele_starttime']));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$line,'xxxxx');
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
