<?php
if(!defined('IN_CRONLITE'))exit();
$title='管理中心'; 
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-cogs"></i>管理中心</a></li>';
include TEMPLATE_ROOT."head.php";

if($islogin==1){
echo '<div class="col-lg-8 col-sm-10 col-xs-12 center-block" role="main">';
echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">管理中心</h3></div><div class="panel-body box">';

if($_GET['my']=='qk'){//清空任务
	$sysid=$_GET['sys'];
	echo '您确认要清空系统'.$sysname[$sysid].'所有任务吗？清空后无法恢复！<br><a href="index.php?mod=set&my=qk2&sys='.$sysid.$link.'">确认</a> | <a href="javascript:history.back();">返回</a>';
}
elseif($_GET['my']=='qk2'){//清空任务结果
	$sysid=intval($_GET['sys']);
	if($DB->query("DELETE FROM ".DBQZ."_wzjob WHERE uid='$uid' and sysid='{$sysid}'")==true){
	echo '清空成功，';
	}else{
	echo '清空失败，<br/>'.$DB->error().'</div>';
	exit;
	}
	echo '无法跳转请 <a href="index.php?mod=list-wz&sys='.$sysid.$link.'">点击刷新</a> !';
	echo "<meta http-equiv='refresh' content='3;url=index.php?mod=list-wz&sys={$sysid}".$link."'>";
}

if($_GET['my']=='qkqd'){//清空签到任务
	echo '您确认要清空所有自动签到任务吗？清空后无法恢复！<br>
	<a href="index.php?mod=set&my=qkqd2'.$link.'">确认</a> | <a href="javascript:history.back();">返回</a>';
}
elseif($_GET['my']=='qkqd2'){//清空任务结果
	if($DB->query("DELETE FROM ".DBQZ."_signjob WHERE uid='$uid'")==true){
	echo '清空成功，';
	}else{
	echo '清空失败，<br/>'.$DB->error().'</div>';
	exit;
	}
	echo '无法跳转请 <a href="index.php?mod=list-sign'.$link.'">点击刷新</a> !';
	echo "<meta http-equiv='refresh' content='3;url=index.php?mod=list-sign".$link."'>";
}

if($_GET['my']=='qkqq'){//清空QQ
	echo '您确认要清空所有QQ账号吗？清空后无法恢复！<br>
	<a href="index.php?mod=set&my=qkqq2'.$link.'">确认</a> | <a href="javascript:history.back();">返回</a>';
}
elseif($_GET['my']=='qkqq2'){//清空QQ结果
	$rs=$DB->query("SELECT qq FROM ".DBQZ."_qq WHERE uid='{$uid}'");
	while($myrow = $DB->fetch($rs)){
		$DB->query("DELETE FROM ".DBQZ."_qqjob WHERE qq='{$myrow['qq']}'");
	}
	if($DB->query("DELETE FROM ".DBQZ."_qq WHERE uid='{$uid}'")){
	$DB->query("UPDATE ".DBQZ."_user SET qqnum= '0' WHERE userid = '$uid'");
	echo '清空成功，';
	}else{
	echo '清空失败，<br/>'.$DB->error().'</div>';
	exit;
}
echo '无法跳转请 <a href="index.php?mod=qqlist'.$link.'">点击刷新</a> !';
echo "<meta http-equiv='refresh' content='3;url=index.php?mod=qqlist".$link."'>";
}
if($_GET['my']=='qkqqrw'){//清空QQ任务
	$qq=$_GET['qq'];
	echo '您确认要清空QQ '.$qq.' 所有任务吗？清空后无法恢复！<br>
	<a href="index.php?mod=set&my=qkqqrw2&qq='.$qq.$link.'">确认</a> | <a href="javascript:history.back();">返回</a>';
}
elseif($_GET['my']=='qkqqrw2'){//清空QQ任务结果
	$qq=daddslashes($_GET['qq']);
	if($DB->query("DELETE FROM ".DBQZ."_qqjob WHERE qq='$qq' and uid='$uid'")==true){
	echo '清空成功，';
	}else{
	echo '清空失败，<br/>'.$DB->error().'</div>';
	exit;
	}
	echo '无法跳转请 <a href="index.php?mod=list-qq&qq='.$qq.$link.'">点击刷新</a> !';
	echo "<meta http-equiv='refresh' content='3;url=index.php?mod=list-qq&qq={$qq}".$link."'>";
}
elseif($_GET['my']=='mm'){
	echo '<form action="index.php" method="get"><input type="hidden" name="my" value="mm2"><input type="hidden" name="mod" value="set">
	<div class="form-group">
	<label>请输入旧密码:</label><br>
	<input type="password" class="form-control" name="m" value=""></div>
	<div class="form-group">
	<label>请输入新密码:</label><br><input type="password" class="form-control" name="mm" value=""></div>
	<div class="form-group">
	<label>重新输入新密码:</label><br><input type="password" class="form-control" name="mm2" value=""></div>
	<input type="submit" class="btn btn-success btn-block" value="修改密码"></form>';
}
elseif($_GET['my']=='mm2'){
	$m=daddslashes($_GET['m']);
	$mm=daddslashes($_GET['mm']);
	$mm2=daddslashes($_GET['mm2']);
	if($_GET['mm']!==$_GET['mm2'])
	{
		echo '<font color="red">两次输入的密码不一致！</font>';
		echo '</div>';
		exit;
	}
	if($mm=='' or $mm2==''){
		echo '新密码不能为空!';
		echo '</div>';
		exit;
	}
	if(!preg_match('/^[a-zA-Z0-9\x7f-\xff]+$/',$mm))
	{
		echo '密码只能为英文、数字与汉字!';
		echo '</div>';
		exit;
	}
	if($row['pass']==$m){
	$sql18="update `".DBQZ."_user` set `pass` ='$mm' where `userid`='$uid'";
	$sds=$DB->query($sql18);
	if($sds){
		echo '修改成功，点击 <a href="index.php?mod=login">重新登录</a>！';
	}else{
		echo '修改失败!<br/>'.$DB->error();
	}
	}else{
		echo '密码错误，修改失败！';
	}
}

elseif($_GET['my']=='mail'){
	if($row['mail_on']==1)$select='<option value="1">1_开启</option><option value="0">0_关闭</option>';
	else $select='<option value="0">0_关闭</option><option value="1">1_开启</option>';
	echo '<form action="index.php" method="get"><input type="hidden" name="my" value="mail2"><input type="hidden" name="mod" value="set">
	<div class="form-group">
	<label>请输入你的邮箱:</label><br>
	<input type="email" class="form-control" name="email" value="'.$row['email'].'"></div>
	<div class="form-group">
	<label>是否开启SID/SKEY失效提醒:</label><br><select class="form-control" name="mail_on"">'.$select.'</select>
	</div>';
	if($row['email'])echo '[<a href="index.php?mod=set&my=mailtest">给 '.$row['email'].' 发一封测试邮件</a>]<br/><br/>';
	echo '<input type="submit" class="btn btn-primary btn-block" value="提交"></form><br/><font color="green">为确保邮件正常接收，请将本站发信邮箱 '.$conf['mail_name'].' 加入到收信白名单！</font>';
}
elseif($_GET['my']=='mailtest'){
	if(!empty($row['email'])){
	$result=send_mail($row['email'],'邮件发送测试。','这是一封测试邮件！<br/><br/>来自：'.$siteurl);
	if($result==1)
		showmsg('邮件发送成功！',1);
	else
		showmsg('邮件发送失败！'.$result,3);
	}
	else
		showmsg('您还未设置邮箱！',3);
}
elseif($_GET['my']=='mail2'){
	$email=daddslashes($_GET['email']);
	$mail_on=daddslashes($_GET['mail_on']);
	if(!empty($email) && !preg_match('/^[A-z0-9._-]+@[A-z0-9._-]+\.[A-z0-9._-]+$/', $email)){
	echo '邮箱格式不正确!';
	echo '</div>';
	exit;
	}
	if(empty($email) && $mail_on==1){
	echo '需要邮箱提醒请先正确填写邮箱！';
	echo '</div>';
	exit;
	}
	$sql18="update `".DBQZ."_user` set `email` ='$email',`mail_on` ='$mail_on' where `user`='$gl'";
	$sds=$DB->query($sql18);
	if($sds){
	showmsg('修改成功！',1);
	}else{
	echo '修改失败!<br/>'.$DB->error();
	}
}

elseif($_GET['my']=='qq'){
	echo '<form action="index.php" method="get"><input type="hidden" name="my" value="qq2"><input type="hidden" name="mod" value="set">
	<div class="form-group">
	<label>请输入你的ＱＱ:</label><br>
	<input type="text" class="form-control" name="qq" value="'.$row['qq'].'"></div>
	<input type="submit" class="btn btn-primary btn-block" value="提交"></form>';
}
elseif($_GET['my']=='qq2'){
	$qq=daddslashes($_GET['qq']);
	if(!empty($qq) && !is_numeric($qq)){
	echo 'ＱＱ格式不正确!';
	echo '</div>';
	exit();
	}
	$sql18="update `".DBQZ."_user` set `qq` ='$qq' where `userid`='$uid'";
	$sds=$DB->query($sql18);
	if($sds){
	showmsg('修改成功！',1);
	}else{
	echo '修改失败!<br/>'.$DB->error();
	}
}

echo '</div></div>';
}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}

include TEMPLATE_ROOT."foot.php";
?>