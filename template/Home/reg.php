<?php
if(!defined('IN_CRONLITE'))exit();

/****注册限制设定****/

$timelimit = 86400; //时间周期(秒)
$iplimit = 3; //相同IP在1个时间周期内限制注册的个数
$verifyswich = 1; //验证码开关
if($isadmin==1)$verifyswich = 0;


if($islogin==1 && $isadmin!=1){
@header('Content-Type: text/html; charset=UTF-8');
exit("<script language='javascript'>alert('请不要重复注册！');history.go(-1);</script>");
}

if($conf['zc']==0 && $isadmin!=1){
@header('Content-Type: text/html; charset=UTF-8');
exit("<script language='javascript'>alert('已停止开放注册服务！');history.go(-1);</script>");
}

if(isset($_GET['invite'])){
	$_SESSION['invite_uid']=$_GET['invite'];
}

if($conf['oauth_open']==2 && $isadmin!=1 && !$_SESSION['Oauth_access_token']){
@header('Content-Type: text/html; charset=UTF-8');
exit("<script language='javascript'>alert('请返回登录页面使用社会化账号登录到本站！');window.location.href='./index.php?mod=login';</script>");
}

$gl=daddslashes(strip_tags($_POST['user']));
$pa=daddslashes(strip_tags($_POST['pass']));
$email=daddslashes(strip_tags($_POST['email']));
$qq=daddslashes(strip_tags($_POST['qq']));
$verifycode=daddslashes(strip_tags($_POST['verify']));

if($_POST['my']=='reg'){
@header('Content-Type: text/html; charset=UTF-8');
if($gl=='' or $pa==''){
exit("<script language='javascript'>alert('帐号或密码不能为空！');history.go(-1);</script>");
}

if($_POST['pass']!==$_POST['pass2'])
{
exit("<script language='javascript'>alert('两次输入的密码不一致！');history.go(-1);</script>");
}

if(preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/',$gl)){}else{
exit("<script language='javascript'>alert('注册失败！用户名只能为英文、数字与汉字！');history.go(-1);</script>");
}
if(preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/',$pa)){}else{
exit("<script language='javascript'>alert('注册失败！密码只能为英文、数字与汉字！');history.go(-1);</script>");
}

if(!preg_match('/^[A-z0-9._-]+@[A-z0-9._-]+\.[A-z0-9._-]+$/', $email) && $conf['zc_mail']==1){
exit("<script language='javascript'>alert('邮箱格式不正确！');history.go(-1);</script>");
}

if(!preg_match('/^[0-9]{5,11}+$/', $qq)){
exit("<script language='javascript'>alert('QQ格式不正确！');history.go(-1);</script>");
}
if($conf['zc_mail']!=1)$email=$qq.'@qq.com';

if($verifyswich==1 && strtolower($verifycode)!=$_SESSION['verifycode']){
exit("<script language='javascript'>alert('验证码不正确！');history.go(-1);</script>");
}

$row2=$DB->get_row("SELECT * FROM ".DBQZ."_user WHERE user='$gl' limit 1");
if($row2['user']==''){}else{
exit("<script language='javascript'>alert('注册失败！此用户名已有用户使用');history.go(-1);</script>");
}

$row2=$DB->get_row("SELECT * FROM ".DBQZ."_user WHERE email='$email' limit 1");
if($row2['user']==''){}else{
if($conf['zc_mail']==1)exit("<script language='javascript'>alert('注册失败！此邮箱已有用户使用');history.go(-1);</script>");
else exit("<script language='javascript'>alert('注册失败！此QQ已有用户使用');history.go(-1);</script>");
}

if($conf['zc']==2 && $isadmin!=1)
{
$timelimits=date("Y-m-d H:i:s",TIMESTAMP+$timelimit);
$ipcount=$DB->count("SELECT count(*) FROM ".DBQZ."_user WHERE `date`<'$timelimits' and `zcip`='$clientip' limit ".$iplimit);
if($ipcount>=$iplimit)
{
exit("<script language='javascript'>alert('注册失败！请不要恶意刷注册！');history.go(-1);</script>");
}
}
if($_SESSION['invite_uid']){
	$DB->query("ALTER TABLE  `".DBQZ."_user` CHANGE `invite` `invite` int(11) NOT NULL DEFAULT 0");
	$udata=$DB->get_row("SELECT * FROM ".DBQZ."_user WHERE zcip='{$clientip}' or dlip='{$clientip}' limit 1");
}

if($_SESSION['Oauth_access_token'] && $_SESSION['Oauth_social_uid'] && $_POST['connect']){
	$srow = $DB->get_row("SELECT * FROM ".DBQZ."_user WHERE social_uid='{$_SESSION['Oauth_social_uid']}' limit 1");
	if($srow['user']) {
		exit("<script language='javascript'>alert('该社会化账号已绑定至本站用户 {$srow['user']}，无法重复绑定！');history.go(-1);</script>");
	}
	$sql="insert into `".DBQZ."_user` (`pass`,`user`,`date`,`last`,`zcip`,`dlip`,`coin`,`email`,`qq`,`peie`,`social_uid`,`social_token`) values ('".$pa."','".$gl."','".$date."','".$date."','".$clientip."','".$clientip."','".$rules[1]."','".$email."','".$qq."','".$conf['peie_free']."','".$_SESSION['Oauth_social_uid']."','".$_SESSION['Oauth_access_token']."')";
	unset($_SESSION['Oauth_access_token']);
	unset($_SESSION['Oauth_social_uid']);
}else{
	$sql="insert into `".DBQZ."_user` (`pass`,`user`,`date`,`last`,`zcip`,`dlip`,`coin`,`email`,`qq`,`peie`) values ('".$pa."','".$gl."','".$date."','".$date."','".$clientip."','".$clientip."','".$rules[1]."','".$email."','".$qq."','".$conf['peie_free']."')";
	//自动赠送VIP一天开始
	$vipdate = date('Y-m-d H:i:s',strtotime('+1 day'));
	$sql3=$DB->query("update ".DBQZ."_user set vip='1',vipdate='$vipdate' where user='".$gl."'");
	//自动赠送VIP一天结尾
}
$uid=$DB->insert($sql);
if($uid){
unset($_SESSION['verifycode']);
if($_SESSION['invite_uid']){//邀请注册
	if(!$udata['user']){
		$DB->query("insert into `".DBQZ."_invite` (`uid`,`reguid`,`regip`,`addtime`) values ('".$_SESSION['invite_uid']."','".$uid."','".$clientip."','".date('Y-m-d')."')");
		$DB->query("update ".DBQZ."_user set invite=invite+1,coin=coin+{$rules[9]} where userid='".$_SESSION['invite_uid']."'");
	}
	unset($_SESSION['invite_uid']);
}
if($isadmin!=1)
exit("<script language='javascript'>alert('注册成功！点击登录！');window.location.href='index.php?mod=user&my=login&user={$gl}&pass={$pa}';</script>");
else
exit("<script language='javascript'>alert('注册成功！');history.go(-1);</script>");
}else{
exit("<script language='javascript'>alert('注册失败！{$DB->error()}');history.go(-1);</script>");
}}

else{
include_once(TEMPLATE_ROOT."zhuce.php");
}



?>