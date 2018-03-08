<?php
 /*
　*刷圈圈赞
*/ 
if(!defined('IN_CRONLITE'))exit();
@header('Content-Type: text/html; charset=UTF-8');
if($islogin==1){
if(in_array('qqz',$vip_func) && $isvip==0 && $isadmin==0) {
	exit('{"code":-1,"msg":"抱歉，您还不是网站VIP会员，无法使用此功能。"}');
}
$qq=daddslashes($_GET['qq']);
if(!$qq) {
	exit('{"code":-1,"msg":"参数不能为空！"}');
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	exit('{"code":-1,"msg":"你只能操作自己的QQ哦！"}');
}

if(!$_SESSION['qqz_'.$qq] || $_SESSION['qqz_'.$qq]<time()){
	if(file_exists(ROOT.'quan.php')) {
		define('RUN_KEY',md5($user.md5($pwd)));
		$qqzurl=$siteurl.'quan.php?runkey='.RUN_KEY.'&qq='.$qq;
		$str=get_curl($qqzurl);
		if($str) {
			$next=time()+60*60*12;
			$_SESSION['qqz_'.$qq]=$next;
			exit('{"code":0,"msg":"'.$str.'"}');
		} else {
			exit('{"code":-2,"msg":"添加失败，接口关闭！"}');
		}
	} elseif($conf['qqz_api']) {
		$qqzurl=$conf['qqz_api'].$qq;
		$str=get_curl($qqzurl);
		$str=mb_convert_encoding($str, "UTF-8", "GB2312");
		//$str=strip_tags($str);
		if($str) {
			$next=time()+60*60*12;
			$_SESSION['qqz_'.$qq]=$next;
			$strs=explode('<style',$str);
			$str=$strs[0];
			$strs=explode('<script',$str);
			$str=str_replace(array("\r\n", "\r", "\n"), "", $strs[0]);
			exit('{"code":0,"msg":"'.$str.'"}');
		} else {
			exit('{"code":-2,"msg":"添加失败，接口关闭！"}');
		}
	} else {
		$qqzurl=$allapi.'api/qqz.php?qq='.$qq.'&url='.$_SERVER['HTTP_HOST'].'&authcode='.$authcode;
		$str=get_curl($qqzurl);
		if($str=='未授权')
			exit('{"code":-2,"msg":"您的网站未授权，授权请联系QQ1277180438"}');
		elseif($str=='none')
			exit('{"code":-2,"msg":"添加失败，接口关闭！"}');
		else{
			$next=time()+60*60*12;
			$_SESSION['qqz_'.$qq]=$next;
			exit('{"code":0,"msg":"'.$str.'"}');
		}
	}
}else{
	exit('{"code":0,"msg":"今天已经添加过，请勿重复添加！"}');
}
}else{
	exit('{"code":-3,"msg":"登录失败，可能是密码错误或者身份失效了，请重新登录"}');
}
?>