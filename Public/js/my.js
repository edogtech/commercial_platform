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
   



})

$('#PageRefresh').click(function() {
	location.reload();
});


