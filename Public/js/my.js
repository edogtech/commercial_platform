var adjustFeeCategory = ""; // 定义充电费/服务费/停车费类型全局变量 

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
	$("#search").click(function () {
		$("#search").attr('placeholder', '');
	})
	$("#time_start").click(function () {
		$("#time_start").attr('placeholder', '');
	})
	$("#time_end").click(function () {
		$("#time_end").attr('placeholder', '');
	});
    // 标题栏-刷新
    $('#PageRefresh').click(function() {
    	location.reload();
    });
    
    /* 充电费/服务费/停车费调价弹框-触发*/
	$('#adjustParkingFee').click(function () {
		adjustFeeCategory="parking";
		
		$('#feeCaption').html("停车费调价");
		$('#feeCaption2').html("停车费");
		$('.unit').html("&nbsp;元/小时");

		var stationID=$(this).attr("stationID"); // 显示费用

		var url="/commercial/index.php/Admin/Terminal/displayFee";
		$.post(url,{stationID:stationID,type:adjustFeeCategory},function(result,status){
			//var data=$.parseJSON(result);
			if (result[0]!==undefined) {
				$("#txtAdjustFee11").val(result[0][0]);
				$("#txtAdjustFee12").val(result[0][1]);
				$("#txtAdjustFee13").val(result[0][2]);
			}else{
				$("#txtAdjustFee11").val('');
				$("#txtAdjustFee12").val('');
				$("#txtAdjustFee13").val('');
			}
			
			if (result[1]!==undefined) {
				$("#txtAdjustFee21").val(result[1][0]);
				$("#txtAdjustFee22").val(result[1][1]);
				$("#txtAdjustFee23").val(result[1][2]);
			}else{
				$("#txtAdjustFee21").val('');
				$("#txtAdjustFee22").val('');
				$("#txtAdjustFee23").val('');
			}
			if (result[2]!==undefined) {
				//alert(result[2])
				$("#txtAdjustFee31").val(result[2][0]);
				$("#txtAdjustFee32").val(result[2][1]);
				$("#txtAdjustFee33").val(result[2][2]);
			}else{
				$("#txtAdjustFee31").val('');
				$("#txtAdjustFee32").val('');
				$("#txtAdjustFee33").val('');
			}
		},'json');
		
		$('#divAdjustFee').css('display','block'); // 弹窗
		$("#test").css('display','block');
			
		
	})
	
	$('#adjustServingFee').click(function () {
		adjustFeeCategory="serving";
		
		$('#feeCaption').html("服务费调价");
		$('#feeCaption2').html("服务费");
		$('.unit').html("&nbsp;元/度");
		
		var stationID=$(this).attr("stationID"); // 显示费用

		var url="/commercial/index.php/Admin/Terminal/displayFee";
		$.post(url,{stationID:stationID,type:adjustFeeCategory},function(result,status){
			//var data=$.parseJSON(result);
			if (result[0]!==undefined) {
				$("#txtAdjustFee11").val(result[0][0]);
				$("#txtAdjustFee12").val(result[0][1]);
				$("#txtAdjustFee13").val(result[0][2]);
			}else{
				$("#txtAdjustFee11").val('');
				$("#txtAdjustFee12").val('');
				$("#txtAdjustFee13").val('');
			}
			
			if (result[1]!==undefined) {
				$("#txtAdjustFee21").val(result[1][0]);
				$("#txtAdjustFee22").val(result[1][1]);
				$("#txtAdjustFee23").val(result[1][2]);
			}else{
				$("#txtAdjustFee21").val('');
				$("#txtAdjustFee22").val('');
				$("#txtAdjustFee23").val('');
			}
			if (result[2]!==undefined) {
				//alert(result[2])
				$("#txtAdjustFee31").val(result[2][0]);
				$("#txtAdjustFee32").val(result[2][1]);
				$("#txtAdjustFee33").val(result[2][2]);
			}else{
				$("#txtAdjustFee31").val('');
				$("#txtAdjustFee32").val('');
				$("#txtAdjustFee33").val('');
			}
		},'json');

		$('#divAdjustFee').css('display','block');
		$("#test").css('display','block');
		
		
	})
	$('#adjustChargingFee').click(function () {
		adjustFeeCategory="charging";
		
		$('#feeCaption').html("充电费调价");
		$('#feeCaption2').html("充电费");
		$('.unit').html("&nbsp;元/度");

		var stationID=$(this).attr("stationID"); // 显示费用

		var url="/commercial/index.php/Admin/Terminal/displayFee";
		$.post(url,{stationID:stationID,type:adjustFeeCategory},function(result,status){
			//var data=$.parseJSON(result);
			if (result[0]!==undefined) {
				$("#txtAdjustFee11").val(result[0][0]);
				$("#txtAdjustFee12").val(result[0][1]);
				$("#txtAdjustFee13").val(result[0][2]);
			}else{
				$("#txtAdjustFee11").val('');
				$("#txtAdjustFee12").val('');
				$("#txtAdjustFee13").val('');
			}
			
			if (result[1]!==undefined) {
				$("#txtAdjustFee21").val(result[1][0]);
				$("#txtAdjustFee22").val(result[1][1]);
				$("#txtAdjustFee23").val(result[1][2]);
			}else{
				$("#txtAdjustFee21").val('');
				$("#txtAdjustFee22").val('');
				$("#txtAdjustFee23").val('');
			}
			if (result[2]!==undefined) {
				//alert(result[2])
				$("#txtAdjustFee31").val(result[2][0]);
				$("#txtAdjustFee32").val(result[2][1]);
				$("#txtAdjustFee33").val(result[2][2]);
			}else{
				$("#txtAdjustFee31").val('');
				$("#txtAdjustFee32").val('');
				$("#txtAdjustFee33").val('');
			}
		},'json');
		
		$('#divAdjustFee').css('display','block');
		$("#test").css('display','block');
		
	})
	
	/*充电费/服务费/停车费调价弹框-校验*/
	$(".divAdjustFeeOK").click(function(){
		var time11=$("#txtAdjustFee11").val();
		var time12=$("#txtAdjustFee12").val();
		var price1=$("#txtAdjustFee13").val();
		
		var time21=$("#txtAdjustFee21").val();
		var time22=$("#txtAdjustFee22").val();
		var price2=$("#txtAdjustFee23").val();
		
		var time31=$("#txtAdjustFee31").val();
		var time32=$("#txtAdjustFee32").val();
		var price3=$("#txtAdjustFee33").val();
	
		// 验证价格与时段必填
		if($.trim(price1).length!=0){
			if($.trim(time11).length==0 && $.trim(time12).length!=0){
				$("#txtAdjustFee11").css('border-color','pink');
				return;
			}
			if($.trim(time11).length!=0 && $.trim(time12).length==0){
				$("#txtAdjustFee12").css('border-color','pink');
				return;
			}
			if($.trim(time11).length==0 && $.trim(time12).length==0){
				$("#txtAdjustFee11,#txtAdjustFee12").css('border-color','pink');
				return;
			}
		}else{
			$("#txtAdjustFee13").css('border-color','pink');
			return;
		}
		
		if($.trim(price2).length!=0){
			if($.trim(time21).length==0 && $.trim(time22).length!=0){
				$("#txtAdjustFee21").css('border-color','pink');
				return;
			}
			if($.trim(time21).length!=0 && $.trim(time22).length==0){
				$("#txtAdjustFee22").css('border-color','pink');
				return;
			}
			if($.trim(time21).length==0 || $.trim(time22).length==0){
				$("#txtAdjustFee21,#txtAdjustFee22").css('border-color','pink');
				return;
			}
		}else{
			if($.trim(time21).length!=0 || $.trim(time22).length!=0){
				$("#txtAdjustFee21,#txtAdjustFee22").css('border-color','pink');
				return;
			}
		}
		
		if($.trim(price3).length!=0){
			if($.trim(time31).length==0 && $.trim(time32).length!=0){
				$("#txtAdjustFee31").css('border-color','pink');
				return;
			}
			if($.trim(time31).length!=0 && $.trim(time32).length==0){
				$("#txtAdjustFee32").css('border-color','pink');
				return;
			}
			if($.trim(time31).length==0 || $.trim(time32).length==0){
				$("#txtAdjustFee31,#txtAdjustFee32").css('border-color','pink');
				return;
			}
		}else{
			if($.trim(time31).length!=0 || $.trim(time32).length!=0){
				$("#txtAdjustFee31,#txtAdjustFee32").css('border-color','pink');
				return;
			}
		}
		
		// 验证正整数
		var exitFlag=false;
		//var myRegExp = /^[0-9]*[1-9][0-9]*$/
		var myRegExp = /^(?![^12].)(?!2[4-9])\d{1,2}$/;
		
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
		
		// 提交
		$("#hiddenFlag").val(adjustFeeCategory);
		$("#frmAdjustFee").submit();
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
		$('#divAdjustFee,#PopControlPile,#PopPendingSheet').css('display','none');
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
		$("#spanDot").append("<img src='/commercial/Public/pic/doting.gif' width='25px'/>");
		
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
		$("#spanDot").append("<img src='/commercial/Public/pic/doting.gif' width='25px'/>");
		
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




