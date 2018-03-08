<?php
session_start();
header("content-Type: text/html; charset=utf-8");

require_once '../cron.inc.php';

if($islogin!=1)exit('{"code":-1,"msg":"未登录"}');
if(in_array('sz',$vip_func) && $isvip==0 && $isadmin==0)exit('{"code":-1,"msg":"您不是VIP，无法使用"}');

if(OPEN_SHUA==0)exit('{"code":-1,"msg":"当前站点未开启此功能"}');

$openid=$_POST['openid'];
$logtype=$_POST['logtype'];
$uin=is_numeric($_POST['uin'])?$_POST['uin']:exit('{"code":-1,"msg":"uin不能为空"}');

$myrow=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$uin}' limit 1");
if($myrow['uid']!=$uid && $isadmin==0)exit('{"code":-1,"msg":"你只能操作自己的QQ哦！"}');


$qid=is_numeric($_POST['qid'])?$_POST['qid']:exit('{"code":-1,"msg":"QID不能为空"}');
$row = $DB->get_row("SELECT * FROM ".DBQZ."_qq where id='{$qid}' limit 1");
if(!$row){
	exit('{"code":-1,"msg":"QID'.$qid.'不存在"}');
}

$gtk=getGTK($row['skey']);
$cookie='pt2gguin=o0'.$row['qq'].'; uin=o0'.$row['qq'].'; skey='.$row['skey'].';';
$ua='Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36';
$url='http://comm.ams.game.qq.com/ams/ame/ame.php?ameVersion=0.3&sServiceType=hbp&iActivityId=111634&sServiceDepartment=group_9&sSDID=dfe7d1122f35c5d5bb00df79db55f096&isXhrPost=true';
if($logtype=='wx'){
	$post='sInviteOpenId='.$openid.'&iInviteAreaId=1&iInvitePlatId=0&appid=wx09dc406043ead215&sServiceType=hbp&iActivityId=111634&iFlowId=364998&g_tk='.$gtk.'&e_code=0&g_code=0&eas_url=http%253A%252F%252Fqiuqiu.qq.com%252Fact%252Fa20161017invite%252Finvite_pc4.htm&eas_refer=http%253A%252F%252Fqiuqiu.qq.com%252Fact%252Fa20161017invite%252Finvite_m4.htm&xhr=1&sServiceDepartment=group_9&xhrPostKey=xhr_1500468477969';
}else{
	$post='sInviteOpenId='.$openid.'&iInviteAreaId=2&iInvitePlatId=1&appid=1105562722&sServiceType=hbp&iActivityId=111634&iFlowId=364998&g_tk='.$gtk.'&e_code=0&g_code=0&eas_url=http%253A%252F%252Fqiuqiu.qq.com%252Fact%252Fa20161017invite%252Finvite_pc4.htm&eas_refer=http%253A%252F%252Fqiuqiu.qq.com%252Fact%252Fa20161017invite%252Finvite_m4.htm&xhr=1&sServiceDepartment=group_9&xhrPostKey=xhr_1500468477969';
}
$data=get_curl($url,$post,'http://apps.game.qq.com/ams/postMessage.html',$cookie,0,$ua);
$arr=json_decode($data,true);
if(@array_key_exists('ret',$arr) && $arr['ret']==0) {
	$_SESSION['q_'.$openid][$row['qq']]=1;
	++$_SESSION['qqcount'];
	exit('{"code":0,"msg":"'.$row[qq].'好友响应成功"}');
} elseif($arr['ret']==600) {
	$_SESSION['q_'.$openid][$row['qq']]=1;
	echo('{"code":-1,"msg":"'.$row[qq].'资格已经用尽"}');
} elseif($arr['ret']==101) {
	$DB->query("UPDATE ".DBQZ."_qq SET status='0' WHERE id='".$row['id']."' limit 1");
	echo('{"code":-3,"msg":"'.$row[qq].'的skey已过期！"}');
} elseif(array_key_exists('code',$arr)) {
	echo('{"code":-2,"msg":"'.$arr["msg"].'"}');
}