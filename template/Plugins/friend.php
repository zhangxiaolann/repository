<?php
 /*
　*批量添加好友
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="批量添加好友";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">批量添加好友</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 center-block" role="main">';

if($islogin==1){
$qq=daddslashes($_GET['qq']);
if(!$qq) {
	showmsg('参数不能为空！');
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	showmsg('你只能操作自己的QQ哦！');
}
if ($row['status']!=1) {
	showmsg('SKEY已过期！');
}
$sid=$row['sid'];
$skey=$row['skey'];
$pskey=$row['pskey'];
?>
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">批量添加好友</h3>
	</div>
	<div class="panel-body box" align="left">
<?php
include ROOT.'qq/qzone.class.php';
$qzone=new qzone($qq,$sid,$skey,$pskey);

if(isset($_POST['uins'])) {
	$groupid=daddslashes($_POST['groupid']);
	echo '<label>添加好友结果:</label><br>';

	$uins = str_replace(array("\r\n", "\r", "\n"), "[br]", $_POST['uins']);
	$match=explode("[br]",$uins);
	foreach($match as $touin) {
		if(!$touin)continue;
		$arr = $qzone->addfriend($touin,$groupid);
		if(array_key_exists('code',$arr))
			echo $touin.'&nbsp;'.$arr['message'].'<br/>';
		else
			echo $touin.'&nbsp;获取结果失败！<br/>';
		flush();
		ob_flush();
	}
	echo '<br/><a href="index.php?mod=friend&qq='.$qq.'"><< 返回上一页</a>';
}else{
	$arr = $qzone->getgroupinfo();
	if (!$arr) {
		showmsg('分组列表获取失败！');
	}elseif ($arr["code"] == -3000) {
		showmsg('SKEY已过期！');
	}elseif ($arr["code"] != 0) {
		showmsg($arr["message"],3);
	}
?>

		<form action="index.php?mod=friend&qq=<?php echo $qq ?>" method="POST">
		<div class="form-group">
		<label>批量添加好友QQ (每行一个):</label><br>
		<textarea class="form-control" name="uins" rows="10" placeholder="此处填写QQ号，每行一个，不能有空行"></textarea>
		<label>分组:</label><br>
		<select name="groupid" class="form-control">
			<?php
			foreach($arr['data']['items'] as $row) {
			echo '<option value="'.$row['groupId'].'">'.$row['groupId'].'_'.$row['groupname'].'</option>';
			}
			?>
			</select>
		<font color="green">一次性添加过多可能会导致访问超时。</font><br/>
		<input type="submit" class="btn btn-primary btn-block" value="确定添加">
		</div>
		</form>
<?php } ?>
	</div>
</div>

<?php
}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}
include TEMPLATE_ROOT."foot.php";
?>