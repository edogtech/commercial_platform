<?php
$config =array(
	//'配置项'=>'配置值'
    'DEFAULT_MODULE'     => 'Admin', //默认模块
    //'DEFAULT_CONTROLLER' => 'Index', // 默认控制器
    //'DEFAULT_ACTION'     => 'index', // 默认操作
    
    /*
     * 0:普通模式 (采用传统癿URL参数模式 )
     * 1:PATHINFO模式(http://<serverName>/appName/module/action/id/1/)
     * 2:REWRITE模式(PATHINFO模式基础上隐藏index.php)
     * 3:兼容模式(普通模式和PATHINFO模式, 可以支持任何的运行环境, 如果你的环境不支持PATHINFO 请设置为3)
     */
    'URL_MODEL'         => 1,
    //伪静态设置
    'URL_HTML_SUFFIX' =>'.html',
    'URL_CASE_INSENSITIVE' =>true, //URL忽略大小写
);

$dbConfig = include_once './Common/Conf/dbconfig.php';//数据库配置
return array_merge($config, $dbConfig);