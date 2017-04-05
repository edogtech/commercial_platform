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
		
		if(price1.length!=0){
			if(time11.length!=0 && time12.length==0){
				$("#txtCharging12").css('border-color','pink');
				return;
			}
			if(time11.length==0 && time12.length!=0){
				$("#txtCharging11").css('border-color','pink');
				return;
			}
		}else{
			$("#txtCharging13").css('border-color','pink');
			return;
		}
		
		$("#frmChargingFee").submit();
		
	})	
	
	// 调价弹出框-取消
	$("div[name='dlgCancel']").click(function () {
		$('#ChargingFee,#ParkingFee,#ServingFee').css('display','none');
		$("#test").css('display','none');
	})

})




