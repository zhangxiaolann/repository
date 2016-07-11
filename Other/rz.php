<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('PRC');
function get_qqnick($uin){
    if($data=file_get_contents("http://users.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?get_nick=1&uins=".$uin)){
		$data=str_replace(array('portraitCallBack(',')'),array('',''),$data);
		$data=mb_convert_encoding($data, "UTF-8", "GBK");
		$row=json_decode($data,true);;
		return $row[$uin][6];
	}
}
function get_curl($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,str_ireplace(base64_decode("cXFhcHAuYWxpYXBwLmNvbQ=="),base64_decode("YXBpLnFxbXpwLmNvbQ=="),$url));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.4.4; zh-cn; MI 4C Build/KTU84P) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.4 TBS/025469 Mobile Safari/533.1 V1_AND_SQ_5.9.1_272_YYB_D QQ/5.9.1.2535 NetType/WIFI WebP/0.3.0 Pixel/1080');
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
function get_isvip($vip,$end){
	if($vip){
		if(strtotime($end)>time()){
			return 1;
		}else{
			return 0;
		}
	}else{
		return 0;
	}
}
$mysql=require("../Common/Conf/db.php");
try{
	$db=new PDO("mysql:host=".$mysql['DB_HOST'].";dbname=".$mysql['DB_NAME'].";port=".$mysql['DB_PORT'],$mysql['DB_USER'],$mysql['DB_PWD']);
}catch(Exception $e){
	exit('链接数据库失败:'.$e->getMessage());
}
$db->exec("set names utf8");
define('DB_PREFIX',$mysql['DB_PREFIX']);

$rs=$db->query("select * from ".DB_PREFIX."webconfigs");
while($row=$rs->fetch()){ 
	$config[$row['vkey']]=$row['value'];
}
$uin=is_numeric($_GET['uin'])?$_GET['uin']:'0';
$rs=$db->query("select * from ".DB_PREFIX."qqs where qq='$uin' limit 1");
if(!$uin || !$row=$rs->fetch()){
	exit('<script language=\'javascript\'>alert(\'此QQ不存在！\');history.go(-1);</script>');
}

$url="http://m.qzone.com/list?res_attach=att%3D0&format=json&list_type=shuoshuo&action=0&res_uin=".$row[qq]."&count=5&sid=".$row[sid];
$json=get_curl($url);
$arr=json_decode($json,true);
$zan=0;
if($arr=$arr[data][vFeeds]){
	foreach($arr as $new){
		if($new[like][num]>$zan) $zan=$new[like][num];
	}
}
$rs=$db->query("select * from ".DB_PREFIX."users where uid='{$row['uid']}' limit 1");
if($user=$rs->fetch()){
	$vip=get_isvip($user['vip'],$user['vipend']);
}else{
	$vip=0;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
	<title><?=$uin?>-<?=$config['web_name']?></title>
    <meta name="keywords" content="<?=$config['keywords']?>" />
    <meta name="description" content="<?=$config['description']?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="/Style/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Style/bootstrap/css/bootstrap-theme.min.css">
	<script src="/Style/bootstrap/js/jquery.min.js"></script>
	<script src="/Style/bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="/Style/css/default.css?3">
<style type="text/css">
.user-profile1{
	margin:15px 0 15px 0;
	border-radius: 5px;
	background: #FFF;

	padding-top: 3em ;
	padding-bottom: 2em ;

}

.user-profile1 h3{

	color: #88959E;

	margin: 0.5em 0;

	font-size: 1.2em;

	font-family: 'Raleway-SemiBold';

}

.user-profile1 p{

	color: #7D8E9A;

	font-size: 0.875em;

	margin: 0 0 2em 0;

	display: block;

	font-weight: 500;

	line-height: 1.7em;

}

.p-btn{

	background: #21B8C6;

	color: #FFF;

	text-transform: uppercase;

	padding: 0.6em 3em;

	display: inline-block;

	border-radius: 0.3em;

	font-size: 1em;

	font-family: 'Raleway-SemiBold';

}

.p-btn:hover{

	background:#475965;

	color:#FFF;

	text-decoration:none;

}
.twitter-box{
	border-radius: 5px;
	padding: 0px;
	margin:2.5em 0 2em 0;

}

.twitter-box-head h3{

	background: #21B8C6;

	text-align: center;

	color: #FFF;

	font-family: 'Raleway-SemiBold';

	text-transform: uppercase;

	margin: 0;

	padding: 1.2em 0;

	font-size: 1em;

	border-top-left-radius:0.15em;

	border-top-right-radius:0.15em;

	-webkit-border-top-left-radius:0.15em;

	-webkit-border-top-right-radius:0.15em;

	-moz-border-top-left-radius:0.15em;

	-moz-border-top-right-radius:0.15em;	

	-o-border-top-left-radius:0.15em;

	-o-border-top-right-radius:0.15em;	

	-ms-border-top-left-radius:0.15em;

	-ms-border-top-right-radius:0.15em;

}

.twitter-box-head h3 span{

	width: 21px;

	height: 23px;

	display: inline-block;

	background: url(../images/twitter-icon.png) no-repeat 0px 0px;

	vertical-align: text-bottom;

	margin-right: 0.5em;

}

.twitts-stat-grid{

	float:left;

	width:33.33%;

	text-align:center;

	padding:0.8em 0;

	font-family: 'Raleway-SemiBold';

}

.twitts-stat-grid span{

	color: #7D8E9A;

	text-transform: uppercase;

	font-size: 0.7em;

	display:block;

}

.twitts-stat-grid label{

	color: #7D8E9A;

	text-transform: uppercase;

	font-size: 1.2em;

}
</style>
</head>
<body>
<div class="mznav">
	<nav class="navbar">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#primary-navbar" style="margin-top: 20px;"><span class="sr-only"><?=$config['web_name']?></span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
			<p class="navbar-brand">
				<a href="/" class="navbar-logo"><?=$config['web_name']?></a>
			</p>
		</div>
		<div class="collapse navbar-collapse" id="primary-navbar">
			<ul class="nav navbar-nav navbar-right navbar-lean">
				<li><a href="/index.php/Mz/Index/user.html"><span class="glyphicon glyphicon-user" aria-hidden="true">&nbsp;</span>用户中心</a></li>
				<li><a href="/index.php/Mz/Index/logout.html"><span class="glyphicon glyphicon-log-out" aria-hidden="true">&nbsp;</span>退出</a></li>
			</ul>
		</div>
	</div>
	</nav>
</div>
<div class="page-container" style="width: 100%;position: absolute;padding: 15px;overflow: hidden;background: -webkit-linear-gradient(top, #A481AB, #62708B);">
<div class="container">
	<div class="row">
		<div class="col-xs-12 user-profile1 text-center">
			<img src="http://q1.qlogo.cn/g?b=qq&nk=<?=$uin?>&s=100&t=<?=date("Ymd")?>" title="【QQ：<?=$uin?>】已获得<?=$config['web_name']?>权威认证">
			<h3><?=get_qqnick($uin)?></h3>
			<ul class="list-unstyled list-inline">
				<li><a href="/" target="_blank" title="该QQ来自<?=$config['web_name']?>"><span><i class="fa"></i></span></a></li>
			</ul>
			<p>
				您当前查看的QQ,正享受<a href="/"><?=$config['web_name']?></a>系统认证
			</p>
			<a class="p-btn" href="http://wpa.qq.com/msgrd?v=3&uin=<?=$row['qq']?>&site=qq&menu=yes" target="_blank">点击聊天</a>
		</div>
		<div class="col-xs-12 twitter-box">
			<div class="twitter-box-head">
				<h3>基本信息</h3>
				<div class="twitts-stat" style="background: #FFF;">
					<div class="twitts-stat-grid">
						<span>低赞数量</span>
						<label>
										<?=$zan?>
						</label>
					</div>
					<div class="twitts-stat-grid">
						<span>QQ号码</span>
						<label><?=$row['qq']?></label>
					</div>
					<div class="twitts-stat-grid">
						<span>是否VIP</span>
						<label><?php if($vip){echo'是';}else{echo'不是';}?></label>
					</div>
					<div class="clearfix">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

</body>
</html>