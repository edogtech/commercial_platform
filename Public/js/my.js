<<<<<<< HEAD
﻿$(function(){
	// 导出
    $('#export').click(function () {
    	 var selectValue=$("#paytype option:selected").val();  //获取Select选择值
    	 var textValue = $("#ordeno").val();//获取text值
    	alert("select值："+selectValue+" text值:"+textValue);
        location.href = 'http://localhost/zuche/admin/ExportOrders/?ordernumber='+textValue+'&paytype='+selectValue;
    })
    
    // 终端管理-查找电站
//    $("#stationSearch").click(function(){
//           var txtStation=$("#txtStation").val();
//           var durl='__APP__/Admin/Terminal/index';
//           $.post(durl,{txtStation:txtStation},function(data){
//        	   $("#result").html(m);
//           });
//    })
   



})

=======
$('#PageRefresh').click(function() {
	location.reload();
});

//$(".status").load(location.href+" .status"); 
>>>>>>> branch 'master' of https://github.com/edogtech/commercial_platform.git
