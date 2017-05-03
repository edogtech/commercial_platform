<?php
namespace Admin\Controller;
use Think\Controller;
class ManagementController extends Controller {
/*
author：z
createtime：17-03-20
@权限管理
@
param：、、、、
*/
    public function __construct(){
        parent::__construct();
        $msg=session('admininfo');
        $uname=$msg['uname'];
        $uid=$msg['uid'];
        $issole=D('UserMerchant')->where(array('uname'=>$uname,'uid'=>$uid))->find();
        if (empty($issole)) {
            $this->error('您无此权限！', 'Index/index', 2);
        }
    }
    public function mindex(){
        /*header*/
        // 在header显示系统当前时间
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());

        // 在header显示系统当前登录的用户名
        $user = strlen($_SESSION['admininfo']['uname']) > 5 ? mb_substr($_SESSION['admininfo']['uname'], 0, 5) . '***' : $_SESSION['admininfo']['uname'];
        $h=$_GET['p']?$_GET['p']:1;
        $msg=session('admininfo');
        $uid=$msg['uid'];
        //print_r($msg['pridlist']);die;
        //$D('UserMerchant')->field('uname')->find();
        $mo=D('PrivRelation');
        $arrr=$mo->table('priv_relation')
            ->field('priv_relation.id,user_info.uid,user_info.level,user_info.uname,priv_relation.privilegeid,priv_relation.mo')
            ->join('user_info on user_info.uid=priv_relation.uid')
            ->page($h.',5')
            ->where(array('user_info.identity'=>$uid))
            ->order('user_info.addtime desc')
            ->select();
        $count=$mo->table('priv_relation')
            ->field('priv_relation.id,user_info.uname,priv_relation.privilegeid')
            ->join('user_info on user_info.uid=priv_relation.uid')
            ->where(array('user_info.identity'=>$uid))
            ->count();
        $arr=array();
        $arr=$arrr;
        foreach($arr as $k => $v){
            $map['pid']=array('in',$v['mo']);
            $quan=D('PrivInfo')->field('pid,privilege')->where($map)->select();
            static $fe1=array();
            static $fe2='';
            foreach ($quan as $k1 =>$v1) {
               $fe1[$k1]['pid']=$v1['pid'];
                $fe1[$k1]['privilege']=$v1['privilege'];
            }
            //分割为数组
            $fe2=explode(',',$v['privilegeid']);
            foreach ($fe1 as $ka => $vq) {
                if($ka>=count($quan)){
                    unset($fe1[$ka]);
                }
            }
            //为$fe1赋值
            //print_r($fe1);
            for($i=0;$i<count($fe1);$i++){
                if(in_array($fe1[$i]['pid'],$fe2)){
                        $fe1[$i]['bi']=$fe1[$i]['pid'];
                }else{
                    $fe1[$i]['bi']='';
                }
            }
           $arr[$k]['privilegename']=$fe1;
        }

        //print_r($arr);die;
        $Page = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记
        
        $show       = $Page->show();// 分页显示输出
       //print_r($arr);die;
        $this->assign('prid',$msg['pridlist']);
        $this->assign('umsg',$msg);
        $this->assign('arr',$arr);
        $this->assign('page',$show);
        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->display('Management/mindex');
    }
    //搜索
    public function searchmana(){
        /*header*/
        // 在header显示系统当前时间
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());

        // 在header显示系统当前登录的用户名
        $user = strlen($_SESSION['admininfo']['uname']) > 5 ? mb_substr($_SESSION['admininfo']['uname'], 0, 5) . '***' : $_SESSION['admininfo']['uname'];
        $searchval=trim(I('get.msearch')==''?'':I('get.msearch','','strip_tags'));
        //echo $searchval;die;
        $msg=session('admininfo');
        $uid=$msg['uid'];
        if($searchval=='请输入内容'){
            $searchval='';
        }
        if (!empty($searchval)) {
            $where['uname']=array('like',"%$searchval%");
        }
        //company identity
        if(empty($uid)){
            $where['identity']=array('eq','');
        }else{
            $where['identity']=array('eq',$uid);
        }

        $mo=D('PrivRelation');

        $i=$_GET['p']?$_GET['p']:1;
        $arrr =$mo->table('priv_relation')
        ->field('priv_relation.id,user_info.uid,user_info.level,user_info.uname,priv_relation.privilegeid,priv_relation.mo')
        ->join("user_info on user_info.uid=priv_relation.uid")
        ->page($i.',5')
        ->where($where)
            ->order('user_info.addtime desc')
        ->select();
        
        // 赋值数据集
        $count      =$mo->table('priv_relation')
        ->field('priv_relation.id,user_info.uid,user_info.level,user_info.uname,priv_relation.privilegeid,priv_relation.mo')
        ->join("user_info on user_info.uid=priv_relation.uid")
        ->where($where)
        ->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页
        $arr=array();
        $arr=$arrr;
        foreach($arr as $k => $v){
            $map['pid']=array('in',$v['mo']);
            $quan=D('PrivInfo')->field('pid,privilege')->where($map)->select();
            static $fe1=array();
            static $fe2='';
            foreach ($quan as $k1 =>$v1) {
               $fe1[$k1]['pid']=$v1['pid'];
                $fe1[$k1]['privilege']=$v1['privilege'];
            }
            //分割为数组
            $fe2=explode(',',$v['privilegeid']);
            foreach ($fe1 as $ka => $vq) {
                if($ka>=count($quan)){
                    unset($fe1[$ka]);
                }
            }
            //为$fe1赋值
            //print_r($fe1);
            for($i=0;$i<count($fe1);$i++){
                if(in_array($fe1[$i]['pid'],$fe2)){
                        $fe1[$i]['bi']=$fe1[$i]['pid'];
                }else{
                    $fe1[$i]['bi']='';
                }
            }
           $arr[$k]['privilegename']=$fe1;
        }
        $this->assign('prid',$msg['pridlist']);
        $this->assign('umsg',$msg);
        $this->assign('arr',$arr);
        $this->assign('page',$show);
        $this->assign('zsearch',$searchval);
        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->display('Management/msearch');
    }
   //添加管理员qqq
    public function addmanage(){
        $msg=session('admininfo');
        $uname=$msg['uname'];
        $identity=$msg['uid'];
        $jname=trim(I('post.uname')==''?'':I('post.uname','','strip_tags'));
        $uname=$jname.'@'.$uname;
        $upswd=trim(I('post.upswd')==''?'':I('post.upswd','','strip_tags'));
        $upswd1=trim(I('post.upswd1')==''?'':I('post.upswd1','','strip_tags'));
        $checkid=implode(',',I('post.ch'));
        if (empty($uname)||empty($upswd)||empty($upswd1)) {
            $this->error('所填项不能为空！');
        }
        if($upswd!=$upswd1){
            /*$ti2[]=1;
            $ti2[]='密码不一致！';
            echo json_encode($ti2,JSON_UNESCAPED_UNICODE);*/
            //echo "<script>location.href='mindex';alert('密码不一致！')</script>";exit();
            $this->error('密码不一致！');
        }
        $mou=D('UserInfo');
        $mou1=$mou->where(array('uname'=>$uname))->find();
        if($mou1){
            /*$ti3[]=1;
            $ti3[]='用户名已存在！';
            echo json_encode($ti3,JSON_UNESCAPED_UNICODE);*/
            //echo "<script>location.href='mindex';alert('用户名已存在！')</script>";exit();
            $this->error('用户名已存在！');
        }
        $data['uname']=$uname;
        $data['upswd']=md5($upswd);
        $data['addtime']=time();
        $data['identity']=$identity;
        $mou2=$mou->table('user_info')->data($data)->add();
        $userid=$mou->table('user_info')->getlastInsID();
        /*echo $userid;die;*/
        if($mou2){
            $da['uid']=$userid;
            $da['privilegeid']=$checkid;
            $da['mo']='1,2,3,4';
            $isins=D('PrivRelation')->data($da)->add();
            if($isins){
                /*$ti4[]=1;
            $ti4[]='添加成功！';
            echo json_encode($ti4,JSON_UNESCAPED_UNICODE);exit;*/
            //echo "<script>location.href='mindex';alert('添加成功！')</script>";exit;
            $this->redirect('Management/mindex');
            }else{
                /*$ti5[]=1;
            $ti5[]='添加权限失败！';
            echo json_encode($ti5,JSON_UNESCAPED_UNICODE);exit;*/
            //echo "<script>location.href='mindex';alert('添加权限失败！')</script>";exit;
            $this->error('添加权限失败！');
            }

        }else{
            /*$ti6[]=1;
            $ti6[]='添加用户失败！';
            echo json_encode($ti6,JSON_UNESCAPED_UNICODE);exit;*/
            //echo "<script>location.href='mindex';alert('添加用户失败！')</script>";exit;
            $this->error('添加用户失败！');
        }
    }
    //删除用户
    public function manadel(){
        $id=I('post.vid');
        $mo=D('PrivRelation');
        $urr=$mo->field('uid')->where(array('id'=>$id))->find();
        $uid=$urr['uid'];
        if (!empty($uid)) {
            $map=array('uid'=>$uid);
        }else{
            $map=array('uid'=>'$uid');
        }
        $ww=array('id'=>$id);
        $a=$mo->where($ww)->delete();
        if ($a) {
            $b=D('UserInfo')->where($map)->delete();
            if ($b) {
                echo "<script>location.href='searchmana';</script>";exit;

            }else{
                echo "<script>location.href='searchmana';alert('删除管理员失败！')</script>";exit;
                //$this->error('删除管理员失败!');
            }
        }else{
            echo "<script>location.href='searchmana';alert('删除失败！')</script>";exit;
            //exit('删除失败！');
            //$this->redirect('Management/gongbiao');
            //$this->error('删除失败!');
        }
    }
    public function gongbiao(){
        $h=$_GET['p']?$_GET['p']:1;
        $mo=D('PrivRelation');
        $arrr=$mo->table('priv_relation')
            ->field('priv_relation.id,user_info.uid,user_info.level,user_info.uname,priv_relation.privilegeid,priv_relation.mo')
            ->join('user_info on user_info.uid=priv_relation.uid')
            ->page($h.',5')
            ->select();
        $count=$mo->table('priv_relation')
            ->field('priv_relation.id,user_info.uname,priv_relation.privilegeid')
            ->join('user_info on user_info.uid=priv_relation.uid')
            ->count();
        $arr=array();
        $arr=$arrr;
        foreach($arr as $k => $v){
            $map['pid']=array('in',$v['mo']);
            $quan=D('PrivInfo')->field('pid,privilege')->where($map)->select();
            static $fe1=array();
            static $fe2='';
            foreach ($quan as $k1 =>$v1) {
               $fe1[$k1]['pid']=$v1['pid'];
                $fe1[$k1]['privilege']=$v1['privilege'];
            }
            //分割为数组
            $fe2=explode(',',$v['privilegeid']);
            foreach ($fe1 as $ka => $vq) {
                if($ka>=count($quan)){
                    unset($fe1[$ka]);
                }
            }
            //为$fe1赋值
            //print_r($fe1);
            for($i=0;$i<count($fe1);$i++){
                if(in_array($fe1[$i]['pid'],$fe2)){
                        $fe1[$i]['bi']=$fe1[$i]['pid'];
                }else{
                    $fe1[$i]['bi']='';
                }
            }
           $arr[$k]['privilegename']=$fe1;
        }

        //print_r($arr);die;
        $Page = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记
        //$Page->setConfig('prev','上一页');
        //$Page->setConfig('next','下一页');
        $show       = $Page->show();// 分页显示输出
       //print_r($arr);die;
        $this->assign('arr',$arr);
        $this->assign('page',$show);
        $this->display('Management/biao');
    }
    //修改用户权限
    public function altmana(){
        $id=I('post.vid');
        $mo=D('PrivRelation');
        $urr=$mo->field('uid,privilegeid,mo')->where(array('id'=>$id))->find();
            //分割为数组
        //echo $urr['mo'];die;
            $map['pid']=array('in',$urr['mo']);
            $quan=D('PrivInfo')->field('pid,privilege')->where($map)->select();
            //print_r($quan);die;
            foreach ($quan as $ke => $v) {
                $fe1[$ke]['pid']=$v['pid'];
                $fe1[$ke]['privilege']=$v['privilege'];
            }
            $fe2=explode(',',$urr['privilegeid']);
            //print_r($fe2);die;
            for($i=0;$i<count($fe1);$i++){
                if(in_array($fe1[$i]['pid'],$fe2)){
                        $fe1[$i]['bi']=$fe1[$i]['pid'];
                }else{
                    $fe1[$i]['bi']='';
                }
            }
            $urr['privilegename']=$fe1;
            //print_r($urr['privilegename']);die;
            $this->assign('urr',$urr);
            $this->display('Management/alter');
    }
    /*添加表单*/
    public function addform(){
        $this->display('Management/addmana');
    }
    /*修改权限*/
    public function altquan(){
        $uid=I('post.uid');
        $privilegeid=implode(',',I('post.mchoice'));
        if (!empty($uid)) {
            $da['uid']=$uid;
        }

        $data['privilegeid']=$privilegeid;
        $a=D('PrivRelation')->where($da)->setField($data);
        if ($a) {
            $this->redirect('Management/mindex');
        }else{
            echo "<script>location.href='mindex';alert('修改权限失败！')</script>";
        }
    }
}
