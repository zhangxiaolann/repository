<?php
error_reporting(0);
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');

function curl_get($url)
{
$ch=curl_init();
$urlarr = parse_url($url);
if($_GET['localcron']==1 && $urlarr['host']==$_SERVER['HTTP_HOST']){
	$url=str_replace('http://'.$_SERVER['HTTP_HOST'].'/','http://127.0.0.1:80/',$url);
	$url=str_replace('https://'.$_SERVER['HTTP_HOST'].'/','https://127.0.0.1:443/',$url);
	$url.='&localcron=1';
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: '.$_SERVER['HTTP_HOST']));
}
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn; R815T Build/JOP40D) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$content=curl_exec($ch);
curl_close($ch);
return($content);
}

function sendsiderr($qq,$skey,$err='skey')
{
global $backurl;
curl_get($backurl.'api.php?my=siderr&qq='.$qq.'&skey='.$skey.'&err='.$err);
}

@set_time_limit(0);
ignore_user_abort(true);
header("content-Type: text/html; charset=utf-8");

include '../config.php';
define('RUN_KEY',md5($dbconfig['user'].md5($dbconfig['pwd'])));

$scriptpath=str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/qq/'));
$siteurl = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$sitepath.'/';

$referer = substr($_SERVER['HTTP_REFERER'], 0, strrpos($_SERVER['HTTP_REFERER'], '/')).'/';

$backurl=strpos($_SERVER['HTTP_REFERER'],'index.php?mod=')?$referer:$_GET['backurl'];
$backurl=empty($backurl)?$siteurl:urldecode($backurl);

if(!isset($_GET['runkey']))$isdisplay=true;

if($siteurl!=$referer){
	if($_GET['runkey']!=md5(RUN_KEY)) {
		exit('您没有访问权限！');
	}
}
?>