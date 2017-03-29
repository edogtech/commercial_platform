<?php
namespace Admin\Controller;
use Think\Controller;
class ManagementController extends Controller {
/*
author：zwm
createtime：17-03-20
@权限管理
@
param：、、、、
*/
    public function mindex(){
        /*header*/
        // 在header显示系统当前时间
        $date= date("Y年m月d日" ,time()).' 星期'.getWeek(time());

        // 在header显示系统当前登录的用户名
        $user=mb_substr($_SESSION['admininfo']['username'],0,4).'**';
        $h=$_GET['p']?$_GET['p']:1;
        $mo=D('PrivRelation');
        $arrr=$mo->table('priv_relation')
            ->field('priv_relation.id,user_info.uid,user_info.level,user_info.uname,priv_relation.privilegeid,priv_relation.mo')
            ->join('user_info on user_info.uid=priv_relation.uid')
            ->page($h.',6')
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
        $Page = new \Think\Page($count,6);// 实例化分页类 传入总记录数和每页显示的记
        
        $show       = $Page->show();// 分页显示输出
       //print_r($arr);die;
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
        $user=mb_substr($_SESSION['admininfo']['username'],0,4).'**';
        $searchval=I('get.msearch');
        //echo $searchval;die;
        if($searchval=='请输入内容'){
            $searchval='';
        }
        if (!empty($searchval)) {
            $where['uname']=array('like',"%$searchval%");
        }
        

        $mo=D('PrivRelation');

        $i=$_GET['p']?$_GET['p']:1;
        $arrr =$mo->table('priv_relation')
        ->field('priv_relation.id,user_info.uid,user_info.level,user_info.uname,priv_relation.privilegeid,priv_relation.mo')
        ->join("user_info on user_info.uid=priv_relation.uid")
        ->page($i.',6')
        ->where($where)
        //->order('user_info.addtime desc')
        ->select();
        
        // 赋值数据集
        $count      =$mo->table('priv_relation')
        ->field('priv_relation.id,user_info.uid,user_info.level,user_info.uname,priv_relation.privilegeid,priv_relation.mo')
        ->join("user_info on user_info.uid=priv_relation.uid")
        ->where($where)
        ->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,6);// 实例化分页类 传入总记录数和每页显示的记录数
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
        $this->assign('arr',$arr);
        $this->assign('page',$show);
        $this->assign('zsearch',$searchval);
        $this->assign('curdate',$date);
        $this->assign('curuser',$user);
        $this->display('Management/msearch');
    }
   //添加管理员
    public function addmanage(){
       $uname=I('post.uname');
        $upswd=I('post.upswd');
        $upswd1=I('post.upswd1');
        $checkid=implode(',',I('post.ch'));
        if (empty($uname)||empty($upswd)||empty($upswd1)) {
            /*$ti1[]=1;
            $ti1[]='所填项不能为空！';
            echo json_encode($ti1,JSON_UNESCAPED_UNICODE);*/
            //$this->redirect('Management/mindex','','所填项不能为空！');
            echo "<script>location.href='mindex';alert('所填项不能为空！')</script>";die();
            //$this->error('');
        }
        if($upswd!=$upswd1){
            /*$ti2[]=1;
            $ti2[]='密码不一致！';
            echo json_encode($ti2,JSON_UNESCAPED_UNICODE);*/
            echo "<script>location.href='mindex';alert('密码不一致！')</script>";exit();
        }
        $mou=D('UserInfo');
        $mou1=$mou->where(array('uname'=>$uname))->find();
        if($mou1){
            /*$ti3[]=1;
            $ti3[]='用户名已存在！';
            echo json_encode($ti3,JSON_UNESCAPED_UNICODE);*/
            echo "<script>location.href='mindex';alert('用户名已存在！')</script>";exit();
        }
        $data['uname']=$uname;
        $data['upswd']=md5($upswd);
        $data['addtime']=time();
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
            echo "<script>location.href='mindex';alert('添加权限失败！')</script>";exit;
            }

        }else{
            /*$ti6[]=1;
            $ti6[]='添加用户失败！';
            echo json_encode($ti6,JSON_UNESCAPED_UNICODE);exit;*/
            echo "<script>location.href='mindex';alert('添加用户失败！')</script>";exit;
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
        }
        $a=D('UserInfo')->where($map)->delete();
        if ($a) {
            $ww=array('id'=>$id);
            $b=$mo->where($ww)->delete();
            if ($b) {
                echo "<script>location.href='searchmana';</script>";exit;

            }else{
                echo "<script>location.href='searchmana';alert('删除权限失败！')</script>";exit;
            }
        }else{
            echo "<script>location.href='searchmana';alert('删除失败！')</script>";exit;
            //exit('删除失败！');
            //$this->redirect('Management/gongbiao');
        }
    }
    public function gongbiao(){
        $h=$_GET['p']?$_GET['p']:1;
        $mo=D('PrivRelation');
        $arrr=$mo->table('priv_relation')
            ->field('priv_relation.id,user_info.uid,user_info.level,user_info.uname,priv_relation.privilegeid,priv_relation.mo')
            ->join('user_info on user_info.uid=priv_relation.uid')
            ->page($h.',6')
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
        $Page = new \Think\Page($count,6);// 实例化分页类 传入总记录数和每页显示的记
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
