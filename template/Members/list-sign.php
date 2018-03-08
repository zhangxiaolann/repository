<?php
 /*
　*　自动签到任务列表
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="自动签到任务列表";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-list-alt"></i>自动签到</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-12" role="main">';

if($islogin==1){

if($conf['active']!=0)phone_check();

if(OPEN_SIGN==0) {
	showmsg('当前站点未开启此功能。',2);exit;
}
$gls=$DB->count("SELECT count(*) from ".DBQZ."_signjob WHERE uid='{$uid}'");
if($row['signjobnum']!=$gls)
	$DB->query("UPDATE ".DBQZ."_user SET signjobnum= '$gls' WHERE userid = '$uid'");

echo '<div class="alert alert-info">★你总共建立了'.$gls.'个自动签到任务！';
if($isvip==0 && $rules[3]!=0 && $isadmin==0)echo '<br/>☆网站签到类任务扣币标准：<font color="red">每条任务每天收取 '.$rules[3].' '.$conf['coin_name'].'</font> 
[<a href="index.php?mod=shop&kind=1">开通VIP</a>]后可以免币';
echo '</div>';
echo '<div id="signfunc">';
echo '
<button href="#" data-toggle="modal" data-target="#signer" class="btn btn-success btn-lg" id="qdlist">添加网站签到任务</button>
<span class="dropdown">
   <button href="#" class="btn btn-info" data-toggle="dropdown" role="button">任务操作 <span class="caret"></span></button>
   <ul class="dropdown-menu" role="menu">
    <li role="presentation"><a role="menuitem" href="index.php?mod=set&my=qkqd'.$link.'">清空所有</a></li>
   </ul>
</span>
';
echo '</div>';
?>

<script>
var $_GET = (function(){
    var url = window.document.location.href.toString();
    var u = url.split("?");
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");
        var get = {};
        for(var i in u){
            var j = u[i].split("=");
            get[j[0]] = j[1];
        }
        return get;
    } else {
        return {};
    }
})();
function showlist(type,page) {
	page = page || '1';
	$('#signfunc').show();
	$('#list').html('<center><i class="fa fa-spinner fa-pulse"></i>正在加载...</center>');
	if($_GET['userid'])
		ajax.get("ajax.php?mod=list&act="+type+"&page="+page+"&userid="+$_GET['userid'], "html", function(data) {
			$('#list').html(data);
		});
	else
		ajax.get("ajax.php?mod=list&act="+type+"&page="+page, "html", function(data) {
			$('#list').html(data);
		});
}
function signjob_edit(type,jobid,page) {
	jobid = jobid || 0;
	page = page || 1;
	$('#signer').modal('hide');
	$('#signfunc').hide();
	$('#list').html('<center><i class="fa fa-spinner fa-pulse"></i>请稍候...</center>');
	ajax.get("ajax.php?mod=signjob&act=edit&type="+type+"&jobid="+jobid+"&page="+page, "html", function(data) {
		$('#list').html(data);
	});
}
function job_edit(act,jobid,table,page) {
	page = page || 1;
	if(act=='del') {
		if(!confirm('你确实要删除此任务吗？'))return false;
	}
	ajax.get("ajax.php?mod=edit&act="+act+"&jobid="+jobid+"&table="+table, "json", function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('signtask',page);
		}else{
			alert(arr.msg);
		}
	});
}
$(document).ready(function(){
$("#qdlist").click(function(){
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=5",async:false});
  $("#myDiv").html(htmlobj.responseText);
});
showlist('signtask',1);
});
function showresult(surl) {
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=8&url="+surl,async:false});
  $("#myDiv").html(htmlobj.responseText);
}
</script>

<style>
.table-responsive>.table>tbody>tr>td,.table-responsive>.table>tbody>tr>th,.table-responsive>.table>tfoot>tr>td,.table-responsive>.table>tfoot>tr>th,.table-responsive>.table>thead>tr>td,.table-responsive>.table>thead>tr>th{white-space: pre-wrap;}
</style>
<div id="list">
</div>
<?php

}
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);
}

include TEMPLATE_ROOT."foot.php";
?>