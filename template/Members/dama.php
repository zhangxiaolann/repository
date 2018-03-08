<?php
 /*
　*　待打码QQ列表
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="待打码QQ列表";

$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-key"></i>协助打码</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-12" role="main">';


if($islogin==1){

if(OPEN_DAMA==0) {
	showmsg('当前站点未开启此功能。',2);exit;
}

$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE status2='4'");

echo '<div class="alert alert-info">★共有 <font color="red">'.$gls.'</font> 个QQ账号等待打码！<br/>
你目前拥有的虚拟币：'.$row['coin'].'</div>';
echo '
<div id="func"><button href="#" class="btn btn-default" data-toggle="modal" data-target="#help">打码说明</button></div>
';

$pagesize=$conf['pagesize'];
if (!isset($_GET['page'])) {
	$page = 1;
	$pageu = $page - 1;
} else {
	$page = $_GET['page'];
	$pageu = ($page - 1) * $pagesize;
}

?>

<div class="modal fade" align="left" id="help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">打码使用说明</h4>
      </div>
      <div class="modal-body">
在这里，你可以帮助网站里的其它友友更新SID&SKEY，同时，你也会得到一定的虚拟币奖励！<br/>
奖励规则：每成功协助打码一次送 <font color="red"><?php echo $rules[5].'</font> '.$conf['coin_name'] ?>。
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function addqq(act,qq) {
	qq = qq || 0;
	$('#func').hide();
	$('#list').html('<center><i class="fa fa-spinner fa-pulse"></i>请稍候...</center>');
	ajax.get("ajax.php?mod=addqq&act="+act+"&qq="+qq, "html", function(data) {
		$('#list').html(data);
	});
}
function showlist(type,page) {
	page = page || '1';
	$('#func').show();
	$('#list').html('<center><i class="fa fa-spinner fa-pulse"></i>正在加载...</center>');
	ajax.get("ajax.php?mod=list&act="+type+"&page="+page, "html", function(data) {
		$('#list').html(data);
	});
}
$(document).ready(function(){
	$("#help").click(function(){
		htmlobj=$.ajax({url:"template/Ajax/display.php?list=11",async:false});
	$("#myDiv").html(htmlobj.responseText);
});
showlist('damalist',1);
});
</script>

<style>
.table-responsive>.table>tbody>tr>td,.table-responsive>.table>tbody>tr>th,.table-responsive>.table>tfoot>tr>td,.table-responsive>.table>tfoot>tr>th,.table-responsive>.table>thead>tr>td,.table-responsive>.table>thead>tr>th{white-space: pre-wrap;}
</style>

<div id="list"></div>
<?php

}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}

include TEMPLATE_ROOT."foot.php";
?>