<?php
if(!defined('IN_CRONLITE'))exit();

$title='手机QQ名片链接生成';
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">艾特二维码生成</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-8 col-sm-10 col-xs-12 center-block" role="main">
<script>

</script>';

if($islogin==1){
vipfunc_check('aite');
$act=isset($_GET['act'])?$_GET['act']:null;

$qq=isset($_GET['qq'])?daddslashes($_GET['qq']):null;

if($act=='add')
{
	$qq=daddslashes($_POST['qq']);
	$title=daddslashes($_POST['title']);
	$data=get_curl($allapi.'api/at.php?act=add&qq='.$qq.'&title='.urlencode($title).'&url='.$_SERVER['HTTP_HOST'].'&authcode='.$authcode);
	$arr=json_decode($data,true);
	if($arr['code']==1) {
		if(isset($_SESSION['aite']) && $array=unserialize($_SESSION['aite'])){
			$array[]=array('qq'=>$qq,'title'=>$title,'url'=>$arr['url']);
			$_SESSION['aite']=serialize($array);
		}else{
			$array=array();
			$array[]=array('qq'=>$qq,'title'=>$title,'url'=>$arr['url']);
			$_SESSION['aite']=serialize($array);
		}
		showmsg('链接生成成功！<hr/>
<div class="input-group">
<div class="input-group-addon">链接内容</div><input type="text" class="form-control" id="url" value="'.$arr['url'].'">
</div>
',1);
	}elseif(array_key_exists('msg',$arr)){
		showmsg($arr['msg'],3);
	}else{
		showmsg($data,4);
	}
}
else
{
?>
<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">手机QQ名片链接说明</h3></div><div class="panel-body">
<p>提交后会生成一个AT链接，在QQ空间中发此链接，别人点击后可以直接进入你的QQ名片。</p>
<p>自定义内容即为链接显示的内容</p>
</div></div>
<div class="panel panel-info">
<div class="panel-heading"><h3 class="panel-title">手机QQ名片链接生成</h3></div>
<div class="panel-body">
<form action="index.php?mod=aite&act=add" method="POST" role="form">
<div class="list-group-item">
<div class="input-group">
<div class="input-group-addon">Q&nbsp;&nbsp;Q&nbsp;&nbsp;号&nbsp;码</div>
<input type="text" class="form-control" name="qq" value="<?php echo $qq?>" placeholder="请输入QQ号码" required>
</div>
</div>
<div class="list-group-item">
<div class="input-group">
<div class="input-group-addon">自定义内容</div>
<input type="text" class="form-control" name="title" value="点此加我好友" placeholder="请输入自定义内容" required>
</div>
</div>
<div class="list-group-item">
<input type="submit" class="btn btn-primary btn-block" value="确认提交">
</div>
</form>
</div>
</div>
<?php if(isset($_SESSION['aite']) && $lists=unserialize($_SESSION['aite'])){?>
<div class="panel panel-success">
<div class="panel-heading"><h3 class="panel-title">我生成的链接</h3></div>
<table class="table table-striped">
<thead><tr><th>ＱＱ</th><th>内容</th><th>链接</th></tr></thead>
<tbody>
<?php
foreach($lists as $row){
	echo '<tr><td>'.$row['qq'].'</td><td>'.$row['title'].'</td><td><a href="'.$row['url'].'" target="_blank">'.$row['url'].'</a></td></tr>';
}
?>
</tbody>
</table>
</div>
<?php
}
}
}else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}

include TEMPLATE_ROOT."foot.php";
?>