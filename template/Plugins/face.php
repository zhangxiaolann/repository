<?php
 /*
　* QQ百变头像
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="QQ百变头像";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">QQ百变头像</a></li>';
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
$sid=$row['sid'];
$skey=$row['skey'];

function getGTK2($skey){
	$salt = 5381;
	$md5key = 'tencentQQVIP123443safde&!%^%1282';
	$hash = array();
	$hash[] = ($salt << 5);
	for($i = 0; $i < strlen($skey); $i ++)
	{
		$ASCIICode = mb_convert_encoding($skey[$i], 'UTF-32BE', 'UTF-8');
		$ASCIICode = hexdec(bin2hex($ASCIICode));
		$hash[] = (($salt << 5) + $ASCIICode);
		$salt = $ASCIICode;
	}
	$md5str = md5(implode($hash) . $md5key);
	return $md5str;
}

$gtk = getGTK($skey);
$cookie="uin=o0" . $qq . "; skey=" . $skey . ";";
$data=get_curl('http://cgi.vip.qq.com/unipay/init?format=json&aid=vipminipay.pingtai.vipsite.nav_new&platform=pc&version=-1&isbreak=0&g_tk='.getGTK2($skey),0,'http://vip.qq.com/',$cookie);
$arr=json_decode($data,true);
if($arr['ret']==-7) {
	showmsg('SKEY已过期！');
}
$isqqvip=$arr['recParam']['is_vip'];

?>
<script>
$(document).ready(function(){
	var i=0;
	$('#start').click(function(){
		var self=$(this);
		self.html('自动换头像中<img src="./images/loading.gif" style="height:15px;">');
		var url="ajax.php?mod=face&qq=<?php echo $qq?>&vip=<?php echo $isqqvip?>";
		ajax.get(url, 'json', function(arr) {
			if(arr.code==1){
				i++;
				$('#result').html('第'+i+'次更换头像成功，当前头像：'+arr.name);
				setTimeout(function () {
				$('#start').click()
				}, 1000);
			}else if(arr.code==0){
				$('#result').html('更换头像失败，原因：'+arr.msg);
				setTimeout(function () {
				$('#start').click()
				}, 1000);
			}else{
				$('#result').html('更换头像失败，原因：'+arr.msg);
			}
		});
	});
});
</script>
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">QQ百变头像</h3>
	</div>
	<div class="panel-body box" align="left">
		<div class="form-group">
		<div class="input-group"><div class="input-group-addon">ＱＱ</div>
			<input type="text" class="form-control" name="qq" value="<?php echo $qq?>" disabled>
		</div></div>
		<div class="form-group">
		<div class="input-group"><div class="input-group-addon">类型</div>
			<input type="text" class="form-control" name="vip" value="<?php echo $isqqvip==1?'QQ会员（动态头像）':'非QQ会员（静态头像）'?>" disabled>
		</div></div>
		<button class="btn btn-primary btn-block" id="start">开始换头像</button><br/>
		<p id="result" style="text-align:center;"></p>
	</div>
</div>
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">使用说明</h3>
	</div>
	<div class="panel-body box" align="left">
		<p style="color:blue">需要保持此页面开启状态，才能自动更换头像。更换头像的频率是1秒，可能会因为网络原因有所延迟。刷新或关闭本页面即可停止自动更换。
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