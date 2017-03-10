<?php
/*
 * @Title: 取当前中文表示的周
 * @access public
 * @param int $date 系统当前时间
 * @return string 中文标识的周
 * @author ZXD
 */
function getWeek($date)
{
    $arr = array('日','一','二','三','四','五','六');
    return $arr[date('w',$date)];
}