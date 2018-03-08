<?php
 /*
　* QQ百变昵称
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="QQ百变昵称";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">QQ百变昵称</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-8 col-sm-10 col-xs-12 center-block" role="main">';

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
if(isset($_SESSION['nick_num']))unset($_SESSION['nick_num']);
$skey=$row['skey'];

?>
<script>
$(document).ready(function(){
	var i=0;
	$('#start').click(function(){
		if (!$('#content').val()){
		return alert('昵称不能为空！');
		}
		var self=$(this);
		self.html('自动换昵称中<img src="./images/loading.gif" style="height:15px;">');
		var url="ajax.php?mod=nick&qq=<?php echo $qq?>&content="+encodeURIComponent($('#content').val());
		ajax.get(url, 'json', function(arr) {
			if(arr.code==1){
				i++;
				$('#result').html('第'+i+'次更换昵称成功，当前昵称：'+arr.nick);
				setTimeout(function () {
				$('#start').click()
				}, 1000);
			}else{
				$('#result').html('更换昵称失败，原因：'+arr.msg);
			}
		});
	});
});
</script>
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">QQ百变昵称</h3>
	</div>
	<div class="panel-body box" align="left">
		<div class="form-group">
		<div class="input-group"><div class="input-group-addon">ＱＱ</div>
			<input type="text" class="form-control" id="qq" value="<?php echo $qq?>" disabled>
		</div></div>
		<div class="form-group">
		<div class="input-group"><div class="input-group-addon">昵称</div>
			<input type="text" class="form-control" id="content" value="" placeholder="多个昵称之前用|隔开">
		</div></div>
		<button class="btn btn-primary btn-block" id="start">开始换昵称</button><br/>
		<p id="result" style="text-align:center;"></p>
	</div>
</div>
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">使用说明</h3>
	</div>
	<div class="panel-body box" align="left">
		<p style="color:blue">需要保持此页面开启状态，才能自动更换昵称。更换昵称的频率是1秒，可能会因为网络原因有所延迟。刷新或关闭本页面即可停止自动更换。
		</p>
	</div>
</div>
<?php
}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}
include TEMPLATE_ROOT."foot.php";
?>