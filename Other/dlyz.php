<?php
@header('Content-Type: text/html; charset=UTF-8');
@error_reporting(E_ALL & ~E_NOTICE);
@date_default_timezone_set('PRC');
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
$result=mysql_query("select * from {$tableqz}webconfigs");
while($row = mysql_fetch_array($result)){ 
	$config[$row['vkey']]=$row['value'];
}
$uin=is_numeric($_GET['uin'])?$_GET['uin']:'0';
if($uin){
	$result=mysql_query("select * from {$tableqz}users where qq='$uin' and daili>0 limit 1");
	if($row = mysql_fetch_array($result)){
		$msg="<div class='alert alert-warning'>恭喜！该QQ({$uin})是本站代理！可以进行交易</div>";
	}else{
		$msg="<div class='alert alert-warning'>警告！该QQ({$uin})不是代理，请结束交易</div>";
	}
}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<title>代理验证-<?=$config['web_name']?></title>
<!--baidu-->
<meta name="baidu-site-verification" content="4IPJiuihDj"/>
<!-- Bootstrap -->
<link href="/Style/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="/Style/bootstrap/js/jquery.min.js"></script>
<script src="/Style/bootstrap/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
<script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<style>
	body{
		margin: 0 auto;
		text-align: center;
	}
	.container {
	  max-width: 580px;
	  padding: 15px;
	  margin: 0 auto;
	}
</style>
<script type="text/javascript">
	  function getValue(obj,str){
	  var input=window.document.getElementById(obj);
	  input.value=str;
	  }
  </script>
</head>
<body>
<div class="container">
	<div class="header">
		<ul class="nav nav-pills pull-right" role="tablist">
			<li role="presentation" class="active"><a href="/">首页</a></li>
			<li role="presentation"><a href="/"><?=$config['web_name']?>
			</a></li>
		</ul>
		<h3 class="text-muted" align="left">代理验证</h3>
	</div>
	<hr><?=$msg?>
	﻿
	<form method="GET" action="?" class="form-sign">
		<div class="input-group">
			<span class="input-group-addon">平台名称</span><input type="text" class="form-control" value="<?=$config['web_name']?>" disabled="ture">
		</div>
		<div class="input-group">
			<span class="input-group-addon">代理扣扣</span>
			<input type="text" class="form-control" name="uin" value="" placeholder="">
		</div>
		<br/>
		<input type="submit" class="btn btn-primary btn-block" value="提交验证">
	</form>
	<p style="text-align:center">
		<br>
		<a href="http://www.qmzan.com"><span class="label label-info"><?=$config['web_name']?></span></a>
		<a href="http://wpa.qq.com/msgrd?v=3&uin=<?=$config['web_qq']?>&site=qq&menu=yes"><span class="label label-info">联系客服</span></a>
	</p>
</div>
</body>
</html>