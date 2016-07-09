<?php
if(!defined('IN_CRONLITE'))exit();
$title="注册";
include_once(TEMPLATE_ROOT."head.php");

/****注册限制设定****/

$timelimit = 86400; //时间周期(秒)
$iplimit = 3; //相同IP在1个时间周期内限制注册的个数
$verifyswich = 1; //验证码开关
if($isadmin==1)$verifyswich = 0;


navi();

echo'<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">免费注册</h3></div>';

if($islogin==1 && $isadmin!=1){
showmsg('请不要重复注册！',3);
exit();
}

if($conf['zc']==0 && $isadmin!=1){
showmsg('已停止开放注册服务！',2);
exit();
}

$gl=daddslashes(strip_tags($_POST['user']));
$pa=daddslashes(strip_tags($_POST['pass']));
$verifycode=daddslashes(strip_tags($_POST['verify']));

if($_POST['my']=='reg'){
if($gl=='' or $pa==''){
showmsg('帐号或密码不能为空！',4);
exit();
}

if($_POST['pass']!==$_POST['pass2'])
{
showmsg('<font color="red">两次输入的密码不一致!</font>',4);
exit;
} 

if(preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/',$gl)){}else{
showmsg('注册失败！<br>失败原因:用户名只能为英文、数字与汉字!',4);
exit();
}
if(preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/',$pa)){}else{
showmsg('注册失败！<br>失败原因:密码只能为英文、数字与汉字!',4);
exit();
}

if($verifyswich==1 && $verifycode!=$_SESSION['verifycode']){
showmsg('注册失败！<br>失败原因:<font color="red">验证码不正确！',4);
exit();
}

$row2=$DB->get_row("SELECT * FROM wjob_user WHERE user='$gl' limit 1");
if($row2['user']==''){}else{
showmsg('注册失败！<br>失败原因:此用户名已有用户使用!',4);
exit();
}

if($conf['zc']==2 && $isadmin!=1)
{
$timelimits=date("Y-m-j H:i:s",TIMESTAMP+$timelimit);
$ipcount=$DB->count("SELECT count(*) FROM wjob_user WHERE `date`<'$timelimits' and `zcip`='$clientip' limit ".$iplimit);
if($ipcount>=$iplimit)
{
showmsg('<font color="red">注册失败！<br>请不要恶意刷注册！</font>',4);
exit();
}
}

$sql="insert into `wjob_user` (`pass`,`user`,`date`,`last`,`zcip`,`dlip`) values ('".$pa."','".$gl."','".$date."','".$date."','".$clientip."','".$clientip."')";
$sds=$DB->query($sql);
if($sds){
showmsg('注册成功!<a href="index.php?mod=user&my=login&user='.$gl.'&pass='.$pa.'">点击登录</a>',1);
unset($_SESSION['verifycode']);
}else{
showmsg('注册失败!<br/>'.$DB->error(),4);
}}

else{
include_once(PUBLIC_ROOT."zhuce.php");
}


echo'<div class="copy">';
echo date("Y年m月d日 H:i:s");
include(ROOT.'includes/foot.php');
echo'</div>';
if($conf['sjyl']==1)
{$txt=file(ROOT.'includes/content/content.db');
shuffle($txt);
echo"$txt[0]";}
echo'</div></div></div></body></html>';

?>