<?php
if(!defined('IN_CRONLITE'))exit();
if(isset($_GET['act']) && $_GET['act']=='qrlogin'){
	if(isset($_SESSION['findpwd_qq']) && $qq=$_SESSION['findpwd_qq']){
		$row=$DB->get_row("SELECT * FROM ".DBQZ."_user WHERE qq='$qq' limit 1");
		if(!$row['user']){
			$qqrow=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='$qq' limit 1");
			if($qqrow['qq']){
				$row=$DB->get_row("SELECT * FROM ".DBQZ."_user WHERE userid='{$qqrow['uid']}' limit 1");
			}
		}
		unset($_SESSION['findpwd_qq']);
		if($row['user']){
			$code=base64_encode(authcode($row['user'].'||||'.time(),'ENCODE',SYS_KEY));
			exit('{"code":1,"url":"index.php?mod=findpwd&code='.urlencode($code).'"}');
		}else{
			exit('{"code":-1,"msg":"QQ不存在"}');
		}
	}else{
		exit('{"code":-2,"msg":"验证失败，请重新扫码"}');
	}
}
$title='找回密码';
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="glyphicon glyphicon-lock"></i>找回密码</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-lg-8 col-sm-10 col-xs-12 center-block" role="main">';

if($islogin==1 && $isadmin!=1){
exit("<script language='javascript'>alert('您已登录！');history.go(-1);</script>");
}

if(isset($_POST['email'])){
$email=daddslashes($_POST['email']);
$verifycode=daddslashes(strip_tags($_POST['verify']));

if(!preg_match('/^[A-z0-9._-]+@[A-z0-9._-]+\.[A-z0-9._-]+$/', $email)){
exit("<script language='javascript'>alert('邮箱格式不正确！');history.go(-1);</script>");
}
if(empty($verifycode) || strtolower($verifycode)!=$_SESSION['verifycode']){
exit("<script language='javascript'>alert('验证码不正确！');history.go(-1);</script>");
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_user WHERE email='$email' limit 1");
if($row['user']==''){
exit("<script language='javascript'>alert('此邮箱不存在！');history.go(-1);</script>");
}
$code=base64_encode(authcode($row['user'].'||||'.time(),'ENCODE',SYS_KEY));
unset($_SESSION['verifycode']);
if(send_mail_findpwd($email, $row['user'], $code))
	exit("<script language='javascript'>alert('重置密码链接已经发送至{$email}！请到邮箱查看连接，重设密码！');history.go(-1);</script>");
else
	exit("<script language='javascript'>alert('邮件发送失败，请联系站长！');history.go(-1);</script>");

}elseif(isset($_GET['code'])){
$code=authcode(base64_decode($_GET['code']),'DECODE',SYS_KEY);
$arr=explode('||||',$code);
$user=daddslashes($arr[0]);
$timestamp=$arr[1];
if($timestamp+3600*24*2<time()){
exit("<script language='javascript'>alert('此链接已失效！');window.location.href='index.php?mod=findpwd';</script>");
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_user WHERE user='$user' limit 1");
if($row['user']==''){
exit("<script language='javascript'>alert('此用户不存在！');window.location.href='index.php?mod=findpwd';</script>");
}

if(isset($_POST['mm'])){
$mm=daddslashes($_POST['mm']);
$mm2=daddslashes($_POST['mm2']);
if($_GET['mm']!==$_GET['mm2'])
	exit("<script language='javascript'>alert('两次输入的密码不一致！');history.go(-1);</script>");
if($mm=='' or $mm2=='')
	exit("<script language='javascript'>alert('新密码不能为空！');history.go(-1);</script>");
if(!preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/',$mm))
	exit("<script language='javascript'>alert('密码只能为英文、数字与汉字！');history.go(-1);</script>");

$sql18="update `".DBQZ."_user` set `pass` ='{$mm}' where `user`='$user'";
$sds=$DB->query($sql18);
if($sds){
showmsg('修改成功！请<a href="index.php?mod=login">重新登录</a>。',1);
}else{
showmsg('修改失败!<br/>'.$DB->error(),4);
}
exit;
}
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">设置一个新密码</h3></div><div class="panel-body box">';
echo '<form action="index.php?mod=findpwd&code='.$_GET['code'].'" method="POST">
<div class="form-group">
<label>用户名:</label><br><input type="text" class="form-control" name="username" value="'.$user.'" disabled></div>
<div class="form-group">
<label>请输入新密码:</label><br><input type="password" class="form-control" name="mm" value=""></div>
<div class="form-group">
<label>重新输入新密码:</label><br><input type="password" class="form-control" name="mm2" value=""></div>
<input type="submit" class="btn btn-success btn-block" value="修改密码"></form>';
echo '</div></div>';

}
else{
?>
<script src="qq/getsid/qrlogin2.js?v=<?php echo VERSION ?>"></script>
<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">找回密码</h3></div>
<div class="panel-body">
<div id="findpwd_mail">
<form action="index.php?mod=findpwd" method="POST">
<div class="form-group">
<label>绑定的邮箱:</label><br>
<input type="email" class="form-control" name="email" value=""></div>
<div class="form-group"><label>验证码: </label><br/><img title="点击刷新" src="verifycode.php" onclick="this.src='verifycode.php?'+Math.random();"><br>
<input type="text" class="form-control" name="verify" value="" autocomplete="off" required></div>
<font color="green">如果收不到邮件请到垃圾信箱查看。</font>
<input type="submit" class="btn btn-primary btn-block" value="找回密码">
<button type="button" id="type_qq" class="btn btn-default btn-block">使用QQ扫码找回密码</button></form>
</div>
<div id="findpwd_qq" style="display:none;">
<div class="list-group" style="text-align: center;">
	<div class="list-group-item list-group-item-info" style="font-weight: bold;" id="login">
		<span id="loginmsg">请使用QQ手机版扫描二维码</span><span id="loginload" style="padding-left: 10px;color: #790909;">.</span>
	</div>
	<div class="list-group-item" id="qrimg">
	</div>
</div>
<button type="button" id="type_mail" class="btn btn-default btn-block">返回使用邮箱找回密码</button>
</div>
</div></div>
<script>
$("#type_qq").click(function(){
	$("#findpwd_mail").css("display","none");
	$("#findpwd_qq").css("display","inherit");
	getqrpic();
	interval1=setInterval(loginload,1000);
	interval2=setInterval(loadScript,3000);
});
$("#type_mail").click(function(){
	cleartime();
	$("#findpwd_mail").css("display","inherit");
	$("#findpwd_qq").css("display","none");
});
</script>
<?php
}


include TEMPLATE_ROOT."foot.php";
?>