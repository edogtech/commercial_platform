$(function(){
	// 导出
    $('#export').click(function () {
    	 var selectValue=$("#paytype option:selected").val();  //获取Select选择值
    	 var textValue = $("#ordeno").val();//获取text值
    	alert("select值："+selectValue+" text值:"+textValue);
        location.href = 'http://localhost/zuche/admin/ExportOrders/?ordernumber='+textValue+'&paytype='+selectValue;
    })
    
    // 终端管理-查找电站
    $("#stationSearch").click(function(){
    	$("#frmStationSearch").submit();
    });
    
    // 终端管理-清空搜索框
    $("#txtStation").click(function(){
    	$("#txtStation").val("");
    });
   
    // 标题栏-刷新
    $('#PageRefresh').click(function() {
    	location.reload();
    });
    
    // 调价弹出框
	$('#adjustParkingFee').click(function () {
		$('#ParkingFee').css('display','block');
		$("#test").css('display','block');
	})
	$('#adjustServingFee').click(function () {
		$('#ServingFee').css('display','block');
		$("#test").css('display','block');
	})
	$('#adjustChargingFee').click(function () {
		$('#ChargingFee').css('display','block');
		$("#test").css('display','block');
	})
	
	// 调价弹出框-确定
	$("#dlgParkingOK").click(function(){
		$("#frmParkingFee").submit();
	})
	$("#dlgServingOK").click(function(){
		$("#frmServingFee").submit();
	})
	$("#dlgChargingOK").click(function(){
		var time11=$("#txtCharging11").val();
		var time12=$("#txtCharging12").val();
		var price1=$("#txtCharging13").val();
		
		var time21=$("#txtCharging21").val();
		var time22=$("#txtCharging22").val();
		var price2=$("#txtCharging23").val();
		
		var time31=$("#txtCharging31").val();
		var time32=$("#txtCharging32").val();
		var price3=$("#txtCharging33").val();
	
		// 验证价格与时段必填
		if($.trim(price1).length!=0){
			if($.trim(time11).length==0 && $.trim(time12).length!=0){
				$("#txtCharging11").css('border-color','pink');
				return;
			}
			if($.trim(time11).length!=0 && $.trim(time12).length==0){
				$("#txtCharging12").css('border-color','pink');
				return;
			}
		}else{
			$("#txtCharging13").css('border-color','pink');
			return;
		}
		
		if($.trim(price2).length!=0){
			if($.trim(time21).length==0 && $.trim(time22).length!=0){
				$("#txtCharging21").css('border-color','pink');
				return;
			}
			if($.trim(time21).length!=0 && $.trim(time22).length==0){
				$("#txtCharging22").css('border-color','pink');
				return;
			}
			if($.trim(time21).length==0 || $.trim(time22).length==0){
				$("#txtCharging21,#txtCharging22").css('border-color','pink');
				return;
			}
		}else{
			if($.trim(time21).length!=0 || $.trim(time22).length!=0){
				$("#txtCharging21,#txtCharging22").css('border-color','pink');
				return;
			}
		}
		
		if($.trim(price3).length!=0){
			if($.trim(time31).length==0 && $.trim(time32).length!=0){
				$("#txtCharging31").css('border-color','pink');
				return;
			}
			if($.trim(time31).length!=0 && $.trim(time32).length==0){
				$("#txtCharging32").css('border-color','pink');
				return;
			}
			if($.trim(time31).length==0 || $.trim(time32).length==0){
				$("#txtCharging31,#txtCharging32").css('border-color','pink');
				return;
			}
		}else{
			if($.trim(time31).length!=0 || $.trim(time32).length!=0){
				$("#txtCharging31,#txtCharging32").css('border-color','pink');
				return;
			}
		}
		
		// 验证正整数
		var exitFlag=false;
		var myRegExp = /^[0-9]*[1-9][0-9]*$/ 
		
		$(".chkReg").each(function(i, field){
			var chkval=field.value;
			if(chkval.length!=0){
				if(!myRegExp.test(chkval)){
					$(this).css('border-color','red');
					exitFlag=true;
					//return; // 此处只能跳出each函数
				}
			}
		})
		if(exitFlag){return;}
		
		// 验证数字
		$(".chkNaN").each(function(index, element){
			var chkNaN=element.value; //$.trim(element.value)
			if(chkNaN.length!=0){
				if(isNaN(chkNaN)){
					$(this).css('border-color','red');
					exitFlag=true;
				}
			}
		})
		if(exitFlag){return;}
		
		$("#frmChargingFee").submit();
		
	})	
	

	// 电桩控制弹框
	$(".DivControlPile").click(function () {
		var pileID=$(this).attr("value"); // 自定义属性value为电桩编号
		$('#PopControlPile').css('display','block');
		$("#test").css('display','block');
		
		var space="<span id='spanDot'>&nbsp</span>"; // 输出占位空格
		$("#divProgress").html(space);
		$("#pileID").attr("value",pileID); //为电桩控制弹窗中id为pileID的hidden赋值

	})
	
	// 弹框-取消
	$("div[name='dlgCancel']").click(function () {
		$('#ChargingFee,#ParkingFee,#ServingFee,#PopControlPile,#PopPendingSheet').css('display','none');
		$("#test").css('display','none');
	})

	// 电桩控制弹窗切换按钮样式
	$('#sp1').click(function(){
		$('#sp1').attr("class", "spanPileCtlSel");
		$('#sp2,#sp3,#sp4').attr("class", "spanPileCtl");
	})
	$('#sp2').click(function(){
		$('#sp2').attr("class", "spanPileCtlSel");
		$('#sp1,#sp3,#sp4').attr("class", "spanPileCtl");
	})
	$('#sp3').click(function(){
		$('#sp3').attr("class", "spanPileCtlSel");
		$('#sp1,#sp2,#sp4').attr("class", "spanPileCtl");
	})
	$('#sp4').click(function(){
		$('#sp4').attr("class", "spanPileCtlSel");
		$('#sp1,#sp2,#sp3').attr("class", "spanPileCtl");
	})
	
	// 电桩控制弹窗-发送指令并返回状态
	
	$("#sp1").click(function(){
		var actionStr=$(this).attr("action");
		var url="/commercial/index.php/Admin/Terminal/controlAction";
		var pileID=$("#pileID").val();
		var userID=$("#userID").val();
		
		$("#spanDot").html("正在开启充电"); // 初始化进度
		$("#spanDot").append("<img src='/commercial/Public/pic/doting.gif' width='25px'/>");
		
		$.post(url,{pileID:pileID,actionStr:actionStr,userID:userID},function(data,status){
			
//			$("#spanDot").html(data); //for dubug
			switch(data){
				case("1"):
					$("#spanDot").html("开启充电成功√");
					$("#spanDot").css("color","#e34747");
				break;
				case("2"):
					$("#spanDot").html("开启充电失败×");
					$("#spanDot").css("color","#e34747");
				break;
			}
		})
	})
	
	$("#sp2").click(function(){
		var actionStr=$(this).attr("action");
		var url="/commercial/index.php/Admin/Terminal/controlAction";
		var pileID=$("#pileID").val();
		var userID=$("#userID").val();
		
		$("#spanDot").html("正在关闭充电"); // 初始化进度
		$("#spanDot").append("<img src='/commercial/Public/pic/doting.gif' width='25px'/>");
		
		$.post(url,{pileID:pileID,actionStr:actionStr,userID:userID},function(data,status){
			switch(data){
				case("1"):
					$("#spanDot").html("关闭充电成功√");
					$("#spanDot").css("color","#e34747");
				break;
				case("2"):
					$("#spanDot").html("关闭充电失败×");
					$("#spanDot").css("color","#e34747");
				break;
			}
		})
	})
	
	$("#sp3").click(function(){
		var actionStr=$(this).attr("action");
		var url="/commercial/index.php/Admin/Terminal/controlAction";
		var pileID=$("#pileID").val();
		var userID=$("#userID").val();
		
		$("#spanDot").html("正在重启电桩"); // 初始化进度
		
		$.post(url,{pileID:pileID,actionStr:actionStr,userID:userID},function(data,status){
			switch(data){
				case("1"):
					$("#spanDot").html("电桩重启成功√");
					$("#spanDot").css("color","red");
				break;
				case("2"):
					$("#spanDot").html("电桩重启失败×");
					$("#spanDot").css("color","#e34747");
				break;
			}
		})
	})
	
	$("#sp4").click(function(){
		var actionStr=$(this).attr("action");
		var url="/commercial/index.php/Admin/Terminal/controlAction";
		var pileID=$("#pileID").val();
		var userID=$("#userID").val();
		
		$("#spanDot").html("正在锁定电桩"); // 初始化进度
		
		$.post(url,{pileID:pileID,actionStr:actionStr,userID:userID},function(data,status){
			switch(data){
				case("1"):
					$("#spanDot").html("电桩锁定成功√");
					$("#spanDot").css("color","red");
				break;
				case("2"):
					$("#spanDot").html("电桩锁定失败×");
					$("#spanDot").css("color","#e34747");
				break;
			}
		})
	})	
	
	
	// 待处理工单弹框
	$(".DivPending").click(function () {
		$("#response").val(""); // 清空问题反馈文本域
		
		$('#PopPendingSheet').css('display','block');
		$("#test").css('display','block');
		var sheetID=$(this).attr("sheetID"); // 自定义属性sheetID为工单ID
		var customer=$(this).attr("customer");
		var phone=$(this).attr("phone");
		var describe=$(this).attr("describe");
		var addtime=$(this).attr("addtime");
		var type=$(this).attr("type");
		
		$('#sheetID').val(sheetID);
		$('#sheetCustomer').val(customer);
		$('#sheetPhone').val(phone);
		$('#sheetDescribe').val(describe);
		$('#sheetAddtime').html(addtime);
		
		switch(type){
			case("1"):
				$("#txtAppException").css("border","red 1px solid");
				$("#txtAppException").css("color","#e34747");
			break;
			case("2"):
				$("#txtPileException").css("color","#e34747");
			break;
			case("3"):
				$("#txtSwipeException").css("color","#e34747");
			break;
			case("4"):
				$("#txtOtherException").css("color","#e34747");
			break;
		}

	})
	
	// 待处理工单弹框-确定
	$("#dlgSheetOK").click(function(){
		var describe=$("#response").val();
		if($.trim(describe).length==0){
			$("#response").css("border","2px solid red");
			return;
		}else{
			$("#frmPendingSheet").submit();
		}
		
	})
	
	// 待处理工单/故障显示标签切换
	$("#divFailure").click(function(){
		location.href="../Maintenance/index";
	})	
	$("#divPendingSheet").click(function(){
		location.href="../Maintenance/worksheet";
	})

	
})




