<?php
if(!defined('IN_CRONLITE'))exit();

$title='加群链接生成';
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">加群链接生成</a></li>';
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

if(isset($_GET['group']))
{
	$group = daddslashes($_GET['group']);
	$gtk = getGTK($row['skey']);
	$cookie = 'pt2gguin=o0'.$qq.'; uin=o0'.$qq.'; skey='.$row['skey'].';';
	$url = 'http://admin.qun.qq.com/cgi-bin/qun_admin/get_join_link';
	$referrer = 'http://admin.qun.qq.com/create/share/index.html?ptlang=2052&groupUin='.$group;
	$post = 'gc='.$group.'&type=1&bkn='.$gtk;
	$data = get_curl($url,$post,$referrer,$cookie);
	$arr = json_decode($data,true);
	if (@array_key_exists('ec',$arr) && $arr['ec']==0) {
		$joinlink = $arr['url'];
	}elseif($arr['ec']==1){
		$errmsg = '加群链接获取失败，原因：SKEY已失效';
	}else{
		$errmsg = '加群链接获取失败 '.$arr['em'];
	}
}
?>
<div class="panel panel-info">
<div class="panel-heading"><h3 class="panel-title">加群链接生成</h3></div>
<div class="panel-body">
<?php if(isset($errmsg)){?>
<div class="alert alert-warning"><?php echo $errmsg?></div>
<?php }?>
<form action="index.php" method="GET" role="form">
<input type="hidden" name="mod" value="joingroup">
<input type="hidden" name="qq" value="<?php echo $qq?>">
<div class="list-group-item">
<div class="input-group">
<div class="input-group-addon">ＱＱ群号</div>
<input type="text" class="form-control" name="group" value="<?php echo $group?>" placeholder="请输入群号" required>
</div>
</div>
<?php if(isset($joinlink)){?>
<div class="list-group-item">
<div class="input-group">
<div class="input-group-addon">加群链接</div>
<input type="text" class="form-control" name="link" value="<?php echo $joinlink?>" disabled>
</div>
</div>
<?php }?>
<div class="list-group-item">
<input type="submit" class="btn btn-primary btn-block" value="立即生成">
</div>
</form>
</div>
</div>
<?php
}else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}

include TEMPLATE_ROOT."foot.php";
?>