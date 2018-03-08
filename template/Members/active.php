<?php
if(!defined('IN_CRONLITE'))exit();
$title='激活账号';
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-mobile-phone"></i>激活账号</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-lg-8 col-sm-10 col-xs-12 center-block" role="main">';

if($islogin==1){

if($_GET['my']=='start'){
$phone=daddslashes($_GET['phone']);
$verifycode=daddslashes(strip_tags($_GET['verify']));

if(isset($_SESSION['smsuid'])) {
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">手机激活账号</h3></div><div class="panel-body box">';
echo $_SESSION['smsmsg'];
echo '<p><form action="index.php" method="GET">
<input type="hidden" name="mod" value="active">
<input type="hidden" name="my" value="verify">
<input type="hidden" name="phone" value="'.$phone.'">
<input type="hidden" name="uid" value="'.$_SESSION['smsuid'].'">
<input type="submit" class="btn btn-success btn-block" value="立即验证"></form></p>
<p><a href="index.php?mod=active" class="btn btn-block btn-default">返回重填</a></p>';
echo '</div></div>';
exit;
}
if(strlen($phone)!=11){
exit("<script language='javascript'>alert('手机号码格式不正确！');history.go(-1);</script>");
}
if(empty($verifycode) || $verifycode!=$_SESSION['verifycode']){
exit("<script language='javascript'>alert('验证码不正确！');history.go(-1);</script>");
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_user WHERE phone='$phone' limit 1");
if($row['user']!=''){
exit("<script language='javascript'>alert('此手机号码已激活过本站账号，请勿重复注册！');history.go(-1);</script>");
}
$json=get_curl($smsapi.'smsapi.php?act=start&url='.urlencode($siteurl).'&authcode='.$authcode.'&phone='.$phone.'&syskey='.SYS_KEY);
$arr=json_decode($json,true);
if($arr['code']==1) {
unset($_SESSION['verifycode']);
$_SESSION['smsuid']=$arr['uid'];
$_SESSION['smsmsg']=$arr['msg'];
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">手机激活账号</h3></div><div class="panel-body box">';
echo $arr['msg'];
echo '<p><form action="index.php" method="GET">
<input type="hidden" name="mod" value="active">
<input type="hidden" name="my" value="verify">
<input type="hidden" name="phone" value="'.$phone.'">
<input type="hidden" name="uid" value="'.$arr['uid'].'">
<input type="submit" class="btn btn-primary btn-block" value="立即验证"></form></p>
<p><a href="index.php?mod=active" class="btn btn-block btn-default">返回重填</a></p>';
echo '</div></div>';
}elseif($arr['code']==-1) {
	showmsg($arr['msg']);exit;
}else{
	showmsg('与API通信失败，请稍后再试');exit;
}

}elseif($_GET['my']=='verify'){
$phone=daddslashes($_GET['phone']);
$uid=daddslashes($_GET['uid']);
$json=get_curl($smsapi.'smsapi.php?act=verify&url='.urlencode($siteurl).'&authcode='.$authcode.'&phone='.$phone.'&uid='.$uid.'&syskey='.SYS_KEY);
$arr=json_decode($json,true);
if($arr['code']==1) {
unset($_SESSION['smsuid']);
unset($_SESSION['smsmsg']);
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">手机激活账号</h3></div><div class="panel-body box">';
echo '<p>恭喜你已通过验证！</p>';
echo '<p><a href="'.$arr['click'].'" class="btn btn-block btn-success">点此立即激活账号</a></p>';
echo '</div></div>';
}elseif($arr['code']==-1) {
	showmsg($arr['msg']);exit;
}else{
	showmsg('与API通信失败，请稍后再试');exit;
}

}elseif($_GET['my']=='doactive'){
$phone=authcode(base64_decode($_GET['code']),'DECODE',SYS_KEY);
if(strlen($phone)!=11 || !is_numeric($phone)){
showmsg('账号激活失败！');exit;
}
$sql18="update `".DBQZ."_user` set `phone` ='{$phone}',`active` ='1' where `user`='$gl'";
$sds=$DB->query($sql18);
if($sds){
showmsg('账号激活成功！<a href="index.php?mod=user">尽情使用吧</a>。',1);
}else{
showmsg('账号激活失败!<br/>'.$DB->error(),4);
}
exit;
}else{
unset($_SESSION['smsuid']);
unset($_SESSION['smsmsg']);
if($row['active']==1) {
	showmsg('您已激活账号！',2);
	exit;
}
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">手机激活账号</h3></div><div class="panel-body box">';
echo '<form action="index.php" method="GET">
<input type="hidden" name="mod" value="active">
<input type="hidden" name="my" value="start">
<div class="form-group">
<label>请输入你的手机号:</label><br>
<input type="text" class="form-control" name="phone" value="" required></div>
<div class="form-group"><label>验证码: </label><br><img title="点击刷新" src="verifycode.php" onclick="this.src=\'verifycode.php?\'+Math.random();"><br>
<input type="text" class="form-control" name="verify" value="" autocomplete="off" required></div>
<input type="submit" class="btn btn-primary btn-block" value="下一步"></form>';
echo '</div></div>';
}

}else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}
include TEMPLATE_ROOT."foot.php";
?>