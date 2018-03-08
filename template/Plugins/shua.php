<?php
if(!defined('IN_CRONLITE'))exit();

//自定义代刷卡密购买地址，留空则系统默认
$kaurl=$conf['shua_kami']?$conf['shua_kami']:'';

$title='QQ空间代刷业务';
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist">ＱＱ管理</a></li>
<li><a href="index.php?mod=list-qq&qq='.$_GET['qq'].'">'.$_GET['qq'].'</a></li>
<li class="active"><a href="#">QQ空间代刷</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-8 col-sm-10 col-xs-12 center-block" role="main">
<script>

</script>';

if($islogin==1){
$act=isset($_GET['act'])?$_GET['act']:null;

$qq=isset($_GET['qq'])?daddslashes($_GET['qq']):null;
?>
<iframe id="preview" src="<?php echo $shuaapi.'mini.php';?>" frameborder="0" scrolling="auto" width="100%" seamless="1"></iframe>
<script>
window.addEventListener('message',function(e){
	var winheight = e.data;
	$("#preview").attr("height", (winheight+20) + "px");
},false);
</script>
<?php
}else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}

include TEMPLATE_ROOT."foot.php";
?>