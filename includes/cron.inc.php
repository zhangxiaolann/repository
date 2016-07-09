<?php
error_reporting(0);
define('IN_CRONLITE', true);
define('IN_CRONJOB', true);
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');

function curl_run($url) {
	$curl=curl_init($url);
 curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
 curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($curl,CURLOPT_TIMEOUT,2);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	curl_exec($curl);
	curl_close($curl);
	return true;
}

function run_fast($sysid) {//秒刷模式运行
global $DB,$t,$seconds,$date,$siteurl;
for($i=1;$i<=$seconds;$i++){
$rs=$DB->query("select * from `wjob_job` where sysid='$sysid' and zt='0' and start<=$t and stop>=$t");
while ($row = $DB->fetch($rs)) {
	$time=time();
	if ($row['time']+$row['pl']<=$time) {
	if ($row['type']==1) { //QQ挂机sid替换
		$qq=$row['proxy'];
		$rows=$DB->get_row("SELECT * FROM wjob_qq WHERE qq='{$qq}' limit 1");
		$row['url']=str_replace('[sid]',$rows['sid'],$row['url']);
	}
	if ($row['type']==3) { //QQ挂机模式
		$qqjob=qqjob_decode($row['url']);
		$row['url']=$qqjob['url'];
		if($row['url']=='no')continue;
	}
	if ($row['type']!=0) {
		$row['post']=1;
		$row['postfields']='backurl='.urlencode($siteurl);
	}
	$curl=curl_init();
	if($row['usep']==1)
	{
	curl_setopt($curl,CURLOPT_PROXY,$row['proxy']);
	}
	curl_setopt($curl,CURLOPT_URL,$row['url']);
	curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,3);
	curl_setopt($curl,CURLOPT_TIMEOUT,1);
	curl_setopt($curl,CURLOPT_NOBODY,1);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl,CURLOPT_AUTOREFERER,1);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
	if ($row['referer']!='')
	{
	curl_setopt($curl,CURLOPT_REFERER,$row['referer']);
	}
	if ($row['useragent']!='')
	{
	curl_setopt($curl,CURLOPT_USERAGENT,$row['useragent']);
	}
	if ($row['cookie']!='')
	{
	curl_setopt($curl,CURLOPT_COOKIE,$row['cookie']);
	}
	if($row['post']==1)
	{
	$postfields=str_replace('[时间]',$date,$row['postfields']);
	curl_setopt($curl,CURLOPT_POST,1);
	curl_setopt($curl,CURLOPT_POSTFIELDS,$postfields);
	}
	curl_exec($curl);
	curl_close($curl);
	$id =$row['jobid'];
	$DB->query("update `wjob_job` set `times`=`times`+1,`timeb`='$date',`time`='$time' where `jobid`='$id'");
	}
}
}
}

function run_basic($sysid) {//普通模式运行
global $DB,$t,$rs,$date,$siteurl;
while ($row = $DB->fetch($rs)) {
	$time=time();
	if ($row['time']+$row['pl']<=$time) {
	if ($row['type']==1) { //QQ挂机sid替换
		$qq=$row['proxy'];
		$rows=$DB->get_row("SELECT * FROM wjob_qq WHERE qq='{$qq}' limit 1");
		$row['url']=str_replace('[sid]',$rows['sid'],$row['url']);
	}
	if ($row['type']==3) { //QQ挂机模式
		$qqjob=qqjob_decode($row['url']);
		$row['url']=$qqjob['url'];
		if($row['url']=='no')continue;
	}
	if ($row['type']!=0) {
		$row['post']=1;
		$row['postfields']='backurl='.urlencode($siteurl);
	}
	$curl=curl_init();
	if($row['usep']==1)
	{
	curl_setopt($curl,CURLOPT_PROXY,$row['proxy']);
	}
	curl_setopt($curl,CURLOPT_URL,$row['url']);
	curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,3);
	curl_setopt($curl,CURLOPT_TIMEOUT,1);
	curl_setopt($curl,CURLOPT_NOBODY,1);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl,CURLOPT_AUTOREFERER,1);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
	if ($row['referer']!='')
	{
	curl_setopt($curl,CURLOPT_REFERER,$row['referer']);
	}
	if ($row['useragent']!='')
	{
	curl_setopt($curl,CURLOPT_USERAGENT,$row['useragent']);
	}
	if ($row['cookie']!='')
	{
	curl_setopt($curl,CURLOPT_COOKIE,$row['cookie']);
	}
	if($row['post']==1)
	{
	$postfields=str_replace('[时间]',$date,$row['postfields']);
	curl_setopt($curl,CURLOPT_POST,1);
	curl_setopt($curl,CURLOPT_POSTFIELDS,$postfields);
	}
	curl_exec($curl);
	curl_close($curl);
	$id =$row['jobid'];
	$DB->query("update `wjob_job` set `times`=`times`+1,`timeb`='$date',`time`='$time' where `jobid`='$id'");
	}
}
}

function dojob(){
$fp=fsockopen($_SERVER["HTTP_HOST"],$_SERVER['SERVER_PORT']);
$out="GET {$_SERVER['PHP_SELF']}?key={$_GET['key']} HTTP/1.0".PHP_EOL;
$out.="Host: {$_SERVER['HTTP_HOST']}".PHP_EOL;
$out.="Connection: Close".PHP_EOL.PHP_EOL;
fputs($fp,$out);
fclose($fp);
}

if (function_exists("set_time_limit"))
{
	@set_time_limit(0);
}
if (function_exists("ignore_user_abort"))
{
	@ignore_user_abort(true);
}

if(defined("SAE_ACCESSKEY"))
include_once ROOT.'includes/sae.php';
else
include_once ROOT.'config.php';

date_default_timezone_set("PRC");
$date=date("Y-m-j H:i:s");
$t=date("H");

if(!isset($port))$port='3306';
//连接数据库
include_once(ROOT."includes/db.class.php");
if(defined('SQLITE'))$DB=new DB($db_file);
else $DB=new DB($host,$user,$pwd,$dbname,$port);

$conf=$DB->get_row("SELECT * FROM wjob_config WHERE id='1' limit 1");//获取系统配置

$siteurl=$conf['siteurl'];
$szie=$conf['interval'];

$seconds=explode('-',$conf['seconds']);
$loop=explode('-',$conf['loop']);
$multi=explode('-',$conf['multi']);

include_once(ROOT."includes/signapi.php");
include_once(ROOT."includes/function.php");
include_once(ROOT."includes/qq.func.php");

if(!empty($conf['cronkey']) && $conf['cronkey']!=$_GET['key'])
exit("CronKey Access Denied!");

if($conf['apiserver']==0)array_map('unlink',glob(ROOT.'sign/cookie*'));
?>