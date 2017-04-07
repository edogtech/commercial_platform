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
	
	// 调价弹出框-取消
	$("div[name='dlgCancel']").click(function () {
		$('#ChargingFee,#ParkingFee,#ServingFee').css('display','none');
		$("#test").css('display','none');
	})

})




