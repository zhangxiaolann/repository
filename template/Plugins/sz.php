<?php
 /*
　*说说刷赞
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="说说刷赞";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">说说刷赞</a></li>';
include TEMPLATE_ROOT."head.php";


if($islogin==1){
if(OPEN_SHUA==0) {
	showmsg('当前站点未开启此功能。',2);exit;
}
vipfunc_check('sz');
$qq=daddslashes($_GET['qq']);
if(!$qq) {
	showmsg('参数不能为空！');
	exit();
}
$row=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($row['uid']!=$uid && $isadmin==0) {
	showmsg('你只能操作自己的QQ哦！');
	exit();
}
if(!isset($_SESSION['szcount']))$_SESSION['szcount']=0;
if($_SESSION['szcount']>100 && $isadmin==0) {
	showmsg('你的刷赞次数已超配额，请明天再来！');
	exit();
}
$skey=$row['skey'];

if($conf['mzjc_api']==0 || !$conf['mzjc_api']) {
$gtk = getGTK($skey);
$cookie="uin=o0" . $qq . "; skey=" . $skey . ";";
$url='http://sh.taotao.qq.com/cgi-bin/emotion_cgi_feedlist_v6?hostUin='.$qq.'&ftype=0&sort=0&pos=0&num=5&replynum=0&code_version=1&format=json&need_private_comment=1&g_tk='.$gtk;
$json = get_curl($url,0,0,$cookie);
}else{
$json = get_curl($allapi.'api/shuo.php?qq='.$qq.'&skey='.$skey.'&authcode='.$authcode);
}
$json=mb_convert_encoding($json, "UTF-8", "UTF-8");
$arr=json_decode($json,true);
//print_r($arr);exit;
if (@array_key_exists('code',$arr) && $arr['code']==0) {
	$shuolist=$arr['msglist'];
}else{
	showmsg('获取说说列表失败！');
	exit();
}
?>
<script>
function showlist(id) {
	$('#szcontrol').html('<center><i class="fa fa-spinner fa-pulse"></i>正在加载...</center>');
	ajax.get("ajax.php?mod=sz&shuoid="+id+"&qq=<?php echo $qq?>", "html", function(data) {
		$('#szcontrol').html(data);
	});
}
$(document).ready(function() {
	$('#startcheck').click(function(){
		$('#load').html('检测中');
		var self=$(this);
		var shuoid=$("#shuoid").val();
		if(shuoid==''){
			alert('说说ID不能为空，请先选择一条说说');
			return false;
		}
		showlist(shuoid);
	});
	$('.cbx').click(function(){
		var shuoid=$(this).val();
		$('#shuoid').val(shuoid);
	});
});
var xiha={
	postData: function(url, parameter, callback, dataType, ajaxType) {
		if(!dataType) dataType='json';
		$.ajax({
			type: "POST",
			url: url,
			async: true,
			dataType: dataType,
			json: "callback",
			data: parameter,
			success: function(data) {
				if (callback == null) {
					return;
				} 
				callback(data);
			},
			error: function(error) {
				alert('创建连接失败');
			}
		});
	}
}
</script>
<div class="col-md-6 col-sm-12">
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">使用说明</h3>
	</div>
	<div class="panel-body box" align="left">
		<p style="color:red">选择要刷的说说，然后点击开始刷赞即可。<br>刷之前请先设置你的QQ空间为所有人可访问。<br>每次随机取出30个QQ，刷新本页面可以更换一批QQ。</p>
		<?php if($rules[8]>0 && $isadmin==0)echo '<p style="color:blue">每刷一次赞扣除 <b>'.$rules[8].'</b> '.$conf['coin_name'].'</p>';?>
	</div>
</div>
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">选择要刷的说说</h3>
	</div>
	<div class="panel-body box">
	<form>
		<?php foreach ($shuolist as $row ) {?>
		<div class="list-group-item">
			<div class="input-group">
				<label><input type='radio' class="cbx" name="cbx" value="<?php echo $row['tid']?>"> <?php echo mb_substr($row['content'],0,32,'utf-8');?></label>
			</div>
		</div>
		<?php }?>
		<div class="panel-footer">
		<font color="blue">本站只列出前五条说说，如果想刷其他说说请自行获取其说说ID。</font>
		</div>
	</form>
	</div>
</div>
</div>
<div class="col-md-6 col-sm-12">
<div class="panel panel-primary">
	<div class="panel-heading w h">
		<h3 class="panel-title" align="center">刷赞控制台</h3>
	</div>
	<div class="panel-body box" align="left">
		<div class="input-group">
		<div class="input-group-addon">要刷的说说ID:</div>
		<input type="text" class="form-control" name="shuoid" value="<?php echo $shuoid?>" id="shuoid">
		</div>
		<button class="btn btn-block btn-warning" id="startcheck">点此开始刷赞</button><br/>
		<div id='szcontrol'></div>
	</div>
</div>
</div>
<?php
}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}
include TEMPLATE_ROOT."foot.php";
?>