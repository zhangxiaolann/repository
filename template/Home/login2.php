<?php
if(!defined('IN_CRONLITE'))exit();
$title="登录";
include TEMPLATE_ROOT."head.php";
if($islogin==1)exit("<script language='javascript'>window.location.href='./index.php?mod=user';</script>");
?>
<div class="col-md-12" style="margin: 0 auto;max-width:580px;">
<div class="panel panel-primary">
	<div class="panel-body" style="text-align: center;">
		<?php if($is_fenzhan==1) $logoname = DBQZ;else $logoname = ''; 
			if(!file_exists(ROOT.'images/'.$logoname.'logo.png')) $logoname='';
		?>
		<img src="images/<?php echo $logoname?>logo.png">
	</div>
</div>
<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">登录你的账号:</h3></div>
<div class="panel-body">
<form action="?" method="get">
<input type="hidden" name="my" value="login">
<div class="input-group">
<span class="input-group-addon">账号</span>
<input type="text" class="form-control" name="user" value="">
</div><br/>
<div class="input-group">
<span class="input-group-addon">密码</span>
<input type="password" class="form-control" name="pass" value="">
</div><br/>
<div class="login-button">
<input type="checkbox" name="ctime" id="ctime" checked="checked" value="2592000" >&nbsp;<label for="ctime">下次自动登录</label>
<a href="index.php?mod=findpwd" class="pull-right" for="ctime">忘记密码？</a><br/><br/>
<button type="submit" class="btn btn-primary btn-block">马上登录</button><br/></form>
<a href="index.php?mod=zhuce2" class="btn btn-success btn-block">注册用户</a>
<?php if($conf['oauth_open']){
	echo '<hr/><div class="text-center col-md-12">';
	$oauth_option=explode("|",$conf['oauth_option']);
	if(in_array('qqdenglu',$oauth_option))echo '<a href="social.php?type=qqdenglu"><img src="assets/img/social/qqdenglu.png"></a>&nbsp;';
	if(in_array('baidu',$oauth_option))echo '<a href="social.php?type=baidu"><img src="assets/img/social/baidu.png"></a>&nbsp;';
	if(in_array('sinaweibo',$oauth_option))echo '<a href="social.php?type=sinaweibo"><img src="assets/img/social/sinaweibo.png"></a>&nbsp;';
	if(in_array('qqweibo',$oauth_option))echo '<a href="social.php?type=qqweibo"><img src="assets/img/social/qqweibo.png"></a>&nbsp;';
	if(in_array('renren',$oauth_option))echo '<a href="social.php?type=renren"><img src="assets/img/social/renren.png"></a>&nbsp;';
	if(in_array('kaixin',$oauth_option))echo '<a href="social.php?type=kaixin"><img src="assets/img/social/kaixin.png"></a>&nbsp;';
	echo '</div>';
}?>
</div>
</div>
</div>
<?php
include TEMPLATE_ROOT."foot.php";
?>