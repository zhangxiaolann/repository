<?php
$mysql=require("../Common/Conf/db.php");
$dbhost=$mysql['DB_HOST'].':'.$mysql['DB_PORT'];
$dbuser=$mysql['DB_USER'];
$dbpassword=$mysql['DB_PWD'];
$dbmysql=$mysql['DB_NAME'];
if($con = mysql_connect($dbhost,$dbuser,$dbpassword)){
	mysql_select_db($dbmysql, $con);
}else{
	exit('数据库链接失败！');
}
mysql_query("set names utf8"); 
$tableqz=$mysql['DB_PREFIX'];