<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>终端管理</title>
<link rel="stylesheet" type="text/css" href="/commercial_platform/Public/css/index.css">
</head>

<body>
	<!--登录判断-->
	<?php if(!isset($_SESSION['admininfo'])): ?><script>
			window.location.href="<?php echo U('Login/login');?>";
		</script><?php endif; ?>
	
	<!-- 菜单栏 -->
	
	<!--菜单栏-->
	<div class="menu" style="float:left;">
		<div class="menu-item" style="height:80px;float:left;background:#E34747;border-right-color:red;border-top-color:red;">
			<img src="/commercial_platform/Public/pic/logo.png" style="float:left;margin-left:10%;margin-top:8%; width:80%;"/>
		</div>
		<div class="menu-item" style="float:left;background:#1C1A1A;">
			<img src="/commercial_platform/Public/pic/terminal_red.png" style="float:left; margin-left:19%;margin-top:18px;width:15px;"/>
			<h5 style="color:#E34648;float:left;margin-left:10px;margin-top:18px;">终端管理</h5>
		</div>
		<div class="menu-item" style="float:left;">
			<img src="/commercial_platform/Public/pic/finance_gray.png" style="float:left; margin-left:19%;margin-top:18px;width:15px;"/>
			<h5 style="color:#959595;float:left;margin-left:10px;margin-top:18px;">财务管理</h5>
		</div>
		<div class="menu-item" style="float:left;">
			<img src="/commercial_platform/Public/pic/market_gray.png" style="float:left; margin-left:19%;margin-top:18px;width:15px;"/>
			<h5 style="color:#959595;float:left;margin-left:10px;margin-top:18px;">经营管理</h5>
		</div>
		<div class="menu-item" style="float:left;">
			<img src="/commercial_platform/Public/pic/operation_gray.png" style="float:left; margin-left:19%;margin-top:18px;width:15px;"/>
			<h5 style="color:#959595;float:left;margin-left:10px;margin-top:18px;">运维管理</h5>
		</div>
		<div class="menu-item" style="float:left;border-bottom:hidden;">
			<img src="/commercial_platform/Public/pic/system_gray.png" style="float:left; margin-left:19%;margin-top:18px;width:15px;"/>
			<h5 style="color:#959595;float:left;margin-left:10px;margin-top:18px;">系统管理</h5>
		</div>
	</div>

	<!-- 标题栏 -->
	
	<!--标题栏-->
	<div class="header" style="float:left;width:1160px">
		<div style=" float:left;font-size:12px; padding-left:468px;margin-top:30px;">
			<label><?php echo ($curdate); ?></label>
		</div>
		<div style=" float:left;font-size:12px; margin-left:50px;margin-top:17px; ">
			<img src="/commercial_platform/Public/pic/user_head.png" style="width:45px;"/>
		</div>
		<div style=" float:left;font-size:12px; margin-left:20px;margin-top:30px; ">
			<label><?php echo ($curuser); ?></label>
		</div>
		<div style=" float:left;font-size:12px; margin-left:60px;margin-top:30px; ">
			<img src="/commercial_platform/Public/pic/help.png" style="width:13px;" />
		</div>
		<div style=" float:left;font-size:12px; margin-left:8px;margin-top:30px; ">
			<a href="javascript:void(0)">帮助</a>
		</div>
		<div style=" float:left;font-size:12px; margin-left:30px;margin-top:30px; ">
			<img src="/commercial_platform/Public/pic/refresh.png" style="width:13px;" />
		</div>
		<div style=" float:left;font-size:12px; margin-left:8px;margin-top:30px; ">
			<label>刷新</label>	
		</div>
		
		<div style=" float:left;font-size:12px; margin-left:30px;margin-top:30px; ">
			<img src="/commercial_platform/Public/pic/message.png" style="width:13px;" />
		</div>
		<div style=" float:left;font-size:12px; margin-left:8px;margin-top:30px; ">
			<label>消息</label>	
		</div>
		
		<div style=" float:left;font-size:12px; margin-left:30px;margin-top:30px; ">
			<img src="/commercial_platform/Public/pic/exit.png" style="width:13px;" />
		</div>
		<div style=" float:left;font-size:12px; margin-left:8px;padding-right:17px;padding-top:30px; ">
			<a href="<?php echo U('Login/logout');?>">退出</a>
		</div>
	
	</div>

	<!-- 页面内容 -->
	
<!--状态栏-->
	<div class="status" style="float:left">
		<div class="status-left" style="float:left" >
			<div style="float:left;font-size:13px;color:white;padding-top:10px;margin-left:55px;">总桩数：<?php echo ($pilesinfo['totalQty']); ?></div>	
			<div style="float:left;font-size:13px;color:white;padding-top:10px;margin-left:65px;">直流桩：<?php echo ($pilesinfo['dcQty']); ?></div>	
			<div style="float:left;font-size:13px;color:white;padding-top:10px;margin-left:65px;">交流桩：<?php echo ($pilesinfo['acQty']); ?></div>	
			<div style="float:left;font-size:13px;color:white;padding-top:10px;margin-left:65px;">今日充电次数：<?php echo ($pilesinfo['times']); ?></div>	
			<div style="float:left;font-size:13px;color:white;padding-top:10px;margin-left:65px;">工作：<?php echo ($pilesinfo['occupyQty']); ?></div>	
			<div style="float:left;font-size:13px;color:white;padding-top:10px;margin-left:65px;">空闲：<?php echo ($pilesinfo['idleQty']); ?></div>	
			<div style="float:left;font-size:13px;color:white;padding-top:10px;margin-left:65px;">故障：<?php echo ($pilesinfo['faultQty']); ?></div>
			
		</div>
		<div class="status-right" style="float:right;">
			<div style="float:left;font-size:13px;color:white;padding-top:10px;padding-left:15px;">充电站</div>
			<div><img src="/commercial_platform/Public/pic/arrow.png" style="padding-top:13px;width:20px;padding-left:9px;" /></div>
		</div>
	</div>
	
	<!--内容-->
	<div class="content" style="float:left;">
		<!--搜索框-->
		<div style="height:40px;margin-top:20px;">
			<div style="float:left;font-size:13px;color:red;padding-top:10px;padding-left:57px;">查询：</font></div>
			<div style="float:left;padding-top:5px;padding-left:18px">
				<input type='text' name='txtSearch'  id="txtSearch" style=" width:220px;height:20px;" value="请输入内容"/>
			</div>
			<div style="float:left;width:25px;height:25px;background-color:red;margin-top:5px;margin-left:20px;">
				<img src="/commercial_platform/Public/pic/search.png" style="width:15px;padding-top:6px;padding-left:6px;"/>
			</div>
		</div>
		<!--数据表格-->
		<div id="termianltbl">
			<table rules="none">
				<tr>
					<th width="10%" >序号</th>
					<th width="20%">充电站名称</th>
					<th width="10%">总电量（度）</th>
					<th width="10%">总桩数（个）</th>
					<th width="15%">今日充电次数（次）</th>
					<th width="25%">充电桩当前状态</th>
					<th width="10%">查看</th>
				</tr>
				<?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td><?php echo ($vo['sequence']); ?></td>
					<td><?php echo ($vo['name']); ?></td>
					<td><?php echo ($vo['electricity_history']); ?></td>
					<td><?php echo ($vo['pile_total']); ?></td>
					<td><?php echo ($vo['times_today']); ?></td>
					<td style="vertical-align:middle;">
						<span style="display:inline-block;background-color:#efa627;width:10px;height:10px;"></span>
						<span style="display:inline-block;">工作：<?php echo ($vo['pile_occupy']); ?></span>
						<span style="display:inline-block;background-color:#48bc24;width:10px;height:10px;"></span>
						<span >空闲：<?php echo ($vo['pile_idle']); ?></span>
						<span style="display:inline-block;background-color:#969696;width:10px;height:10px;"></span>
						<span>故障：<?php echo ($vo['pile_fault']); ?></span>
					</td>
					<td>
						<div><img src="/commercial_platform/Public/pic/examine.png" style="width:15px;padding-top:6px;padding-left:6px;"/></div>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>			
			</table>
		</div>
		
	</div>

	

</body>
</html>