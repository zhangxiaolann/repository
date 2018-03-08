<?php
 /*
　*　用户资料文件
*/
if(!defined('IN_CRONLITE'))exit();
$title="用户资料";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-user"></i>用户资料</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 center-block" role="main">';

echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">用户资料</h3></div>';
echo '<div class="panel-body box">';
if($isvip==1)$vipstatus='到期时间:<font color="green">'.$row['vipdate'].'</font>';
elseif($isvip==2)$vipstatus='<font color="green">永久 VIP</font>';
else $vipstatus='<font color="red">非 VIP</font>';
echo '<li class="list-group-item"><b>UID：</b>'.$row['userid'].'</li>
<li class="list-group-item"><b>用户名：</b>'.$row['user'].'</li>
<li class="list-group-item"><b>用户组：</b>'.usergroup().'</li>
<li class="list-group-item"><b>注册日期：</b>'.$row['date'].'</li>
<li class="list-group-item"><b>'.$conf['coin_name'].'：</b><font color="red">'.$row['coin'].'</font> [<a href="index.php?mod=shop&kind=1">在线充值</a>]</li>
<li class="list-group-item"><b>VIP状态：</b>'.$vipstatus.' [<a href="index.php?mod=shop&kind=2">开通/续费VIP</a>]</li>
<li class="list-group-item"><b>密码：</b>********* [<a href="index.php?mod=set&my=mm">修改密码</a>]</li>';
if($conf['oauth_open'])echo '<li class="list-group-item"><b>社会化账号：</b>'.($row['social_uid']?'<font color="green">已绑定，你现在可以快捷登录到本站</font>':'<font color="red">未绑定</font> <a href="#" data-toggle="modal" data-target="#bind" class="btn btn-default btn-xs" id="bind">立即绑定</a>').'</li>';
echo '</div></div>';

echo '<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">修改资料</h3></div>';
echo '<div class="panel-body box">';
echo '<form action="index.php?mod=userinfo" method="post">
		<div class="form-group">
		<div class="input-group"><div class="input-group-addon">邮箱</div>
			<input type="text" class="form-control" name="email" value="'.$row['email'].'" disabled>
			<div class="input-group-addon"><a href="index.php?mod=set&my=mail">修改</a></div>
		</div></div>
		<div class="form-group">
		<div class="input-group"><div class="input-group-addon">ＱＱ</div>
			<input type="text" class="form-control" name="qq" value="'.$row['qq'].'" placeholder="用于显示头像" disabled>
			<div class="input-group-addon"><a href="index.php?mod=set&my=qq">修改</a></div>
		</div></div>
		<div class="form-group">
		<div class="input-group"><div class="input-group-addon">手机</div>
			<input type="text" class="form-control" name="phone" value="'.$row['phone'].'" disabled>
			<div class="input-group-addon"><a href="index.php?mod=active">绑定</a></div>
		</div>
		</div>';
echo '</div></div>';
?>
<script>
$(document).ready(function(){
	$("#bind").click(function(){
		htmlobj=$.ajax({url:"template/Ajax/display.php?list=10&option=<?php echo $conf['oauth_option']?>",async:false});
	$("#myDiv").html(htmlobj.responseText);
});
});
</script>
<?php
include TEMPLATE_ROOT."foot.php";
?>