<?php
//协助打码QQ登录获取SID
if(!defined('IN_CRONLITE'))exit();

/*
 *系统QQ状态码(SID&SKEY)说明：
 *status=0 失效
 *status=1 正常
 *status=4 待打码
 *status=5 无法打码
*/
$uin=empty($_POST['uin'])?exit('{"saveOK":-1,"msg":"uin不能为空"}'):daddslashes($_POST['uin']);
$vcode=empty($_POST['vcode'])?exit('{"saveOK":-1,"msg":"vcode不能为空"}'):strtoupper($_POST['vcode']);
$pt_verifysession=empty($_POST['pt_verifysession'])?exit('{"saveOK":-1,"msg":"pt_verifysession不能为空"}'):$_POST['pt_verifysession'];

$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$uin}' limit 1");
if($row['status2']==1 && $row['status']==1)exit('{"saveOK":-1,"msg":"当前QQ未失效"}');

$pw=authcode($row['pw'],'DECODE',SYS_KEY);
$p=get_curl('http://encode.qqzzz.net/?type=1&uin='.$uin.'&pwd='.strtoupper(md5($pw)).'&vcode='.strtoupper($vcode));
if($p=='error'||$p=='')exit('{"saveOK":-1,"msg":"p值获取失败"}');

if(strpos('s'.$vcode,'!')){
	$v1=0;
}else{
	$v1=1;
}
$url='http://ptlogin.qq.com/login?verifycode='.strtoupper($vcode).'&u='.$uin.'&p='.$p.'&pt_randsalt=2&ptlang=2052&low_login_enable=0&u1=http%3A%2F%2Fh5.qzone.qq.com%2Fmqzone%2Findex%3Fg_f%3D&from_ui=1&fp=loginerroralert&device=2&aid=549000912&daid=5&pt_ttype=1&pt_3rd_aid=0&ptredirect=0&h=1&g=1&pt_uistyle=9&regmaster=&pt_vcode_v1='.$v1.'&pt_verifysession_v1='.$pt_verifysession.'&';
$ret = get_curl($url,0,0,0,1);
if(preg_match("/ptuiCB\('(.*?)'\)/", $ret, $arr)){
	$r=explode("','",str_replace("', '","','",$arr[1]));
	if($r[0]==0){
		if(strpos($r[2],'mibao_vry')){
			$DB->query("UPDATE ".DBQZ."_qq SET status='5',status2='5' WHERE qq='".$uin."'");
			exit('{"saveOK":-3,"msg":"该QQ开启了网页保护！"}');
		}
		preg_match('/skey=@(.{9});/',$ret,$skey);
		preg_match('/superkey=(.*?);/',$ret,$superkey);
		$data=get_curl($r[2],0,0,0,1);
		if($data) {
			preg_match("/p_skey=(.*?);/", $data, $matchs);
			$pskey = $matchs[1];
			preg_match("/Location: (.*?)\r\n/iU", $data, $matchs);
			$sid=explode('sid=',$matchs[1]);
			$sid=$sid[1];
		}
		if($skey[1] && $pskey){
			$DB->query("update `".DBQZ."_qq` set `sid` ='{$sid}',`skey` ='@{$skey[1]}',`pskey` ='{$pskey}',`superkey` ='{$superkey[1]}',`status` ='1',`status2` ='1' where `qq`='{$uin}'");
			if($conf['jifen']==1) {
				$DB->query("update ".DBQZ."_user set coin=coin+{$rules[5]} where userid='".$uid."'");
				$DB->query("update ".DBQZ."_user set coin=coin-{$rules[6]} where userid='".$row['uid']."'");
			}
			exit('{"saveOK":0,"uin":"'.$uin.'"}');
		}else{
			exit('{"saveOK":-3,"msg":"登录成功，获取pskey失败！"}');
		}
	}elseif($r[0]==4){
		exit('{"saveOK":4,"uin":"'.$uin.'","msg":"验证码错误"}');
	}elseif($r[0]==3){
		$DB->query("UPDATE ".DBQZ."_qq SET status='5',status2='5' WHERE qq='".$uin."'");
//		$DB->query("DELETE FROM ".DBQZ."_qq WHERE qq='$uin'");
//		$DB->query("DELETE FROM ".DBQZ."_qqjob WHERE qq='$uin'");
		exit('{"saveOK":3,"uin":"'.$uin.'","msg":"密码错误"}');
	}elseif($r[0]==19){
		$DB->query("UPDATE ".DBQZ."_qq SET status='5',status2='5' WHERE qq='".$uin."'");
		exit('{"saveOK":19,"uin":"'.$uin.'","msg":"您的帐号暂时无法登录，请到 http://aq.qq.com/007 恢复正常使用"}');
	}else{
		$DB->query("UPDATE ".DBQZ."_qq SET status='5',status2='5' WHERE qq='".$uin."'");
		exit('{"saveOK":-6,"msg":"'.str_replace('"','\'',$r[4]).'"}');
	}
}else{
	exit('{"saveOK":-2,"msg":"'.$ret.'"}');
}

?>