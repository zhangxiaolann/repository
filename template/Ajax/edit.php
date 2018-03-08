<?php
if(!defined('IN_CRONLITE'))exit();

$act=isset($_GET['act'])?$_GET['act']:null;

if($islogin==1)
{
$jobid=isset($_GET['jobid'])?daddslashes($_GET['jobid']):exit('No jobid!');
$table=isset($_GET['table'])?daddslashes($_GET['table']):exit('No table!');

if($act=='del'||$act=='kq'||$act=='zt')
	$row1=$DB->get_row("SELECT *FROM ".DBQZ."_{$table} where jobid='{$jobid}' limit 1");
else
	$row1=$DB->get_row("SELECT *FROM ".DBQZ."_qq where qq='{$jobid}' limit 1");
if($row1['uid']!=$uid && $isadmin==0 && $isdeputy==0)
	exit('{"code":0,"msg":"你只能操作自己的任务哦！"}');

switch($act) {

case 'del':
	$sql="DELETE FROM ".DBQZ."_{$table} WHERE jobid='$jobid'";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"任务删除成功！"}');
	}else{
		exit('{"code":0,"msg":"任务删除失败！'.$DB->error().'"}');
	}
break;

case 'kq':
	$sql="update `".DBQZ."_{$table}` set `zt` ='0',`day` ='1' where `jobid`='$jobid'";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"任务开启成功！"}');
	}else{
		exit('{"code":0,"msg":"任务开启失败！'.$DB->error().'"}');
	}
break;

case 'zt':
	$sql="update `".DBQZ."_{$table}` set `zt` ='1' where `jobid`='$jobid'";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"任务暂停成功！"}');
	}else{
		exit('{"code":0,"msg":"任务暂停失败！'.$DB->error().'"}');
	}
break;

case 'kqall':
	$sql="update `".DBQZ."_{$table}` set `zt` ='0',`day` ='1' where `qq`='$jobid'";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"QQ'.$jobid.'的所有任务开启成功！"}');
	}else{
		exit('{"code":0,"msg":"QQ'.$jobid.'的所有任务开启失败！'.$DB->error().'"}');
	}
break;

case 'ztall':
	$sql="update `".DBQZ."_{$table}` set `zt` ='1' where `qq`='$jobid'";
	if($DB->query($sql)){
		exit('{"code":1,"msg":"QQ'.$jobid.'的所有任务暂停成功！"}');
	}else{
		exit('{"code":0,"msg":"QQ'.$jobid.'的所有任务暂停失败！'.$DB->error().'"}');
	}
break;

}
}else{
	exit('{"code":-1,"msg":"登录失败，可能是密码错误或者身份失效了，请重新登录！"}');
}