<?php
//数据库配置
$arr1 = array(
    'DB_TYPE'           => 'mysql',
    'DB_HOST'           => '127.0.0.1',
    'DB_NAME'           => 'e_chongdian',
    'DB_USER'           => 'root4',
    'DB_PWD'            => 'yydl',
    'DB_PORT'           => '3306',
    'DB_PREFIX'         => 'e_',

);

$arr2 = array(
    //File
    'DATA_CACHE_TYPE'                   => 'File',
    'DATA_CACHE_TIME'                   => 5,
    'DATA_CACHE_PREFIX'                   => 'e_',
    // 'DATA_CACHE_TIMEOUT'                   => false,
);

return array_merge($arr1,$arr2);//本机、内网