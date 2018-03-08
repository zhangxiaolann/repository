<?php
 /*
　*　网址监控任务列表
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="网址监控任务列表";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-list-alt"></i>网址监控</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-12" role="main">';

if($islogin==1){

if($conf['active']!=0)phone_check();

if(OPEN_CRON==0) {
	showmsg('当前站点未开启此功能。',2);exit;
}
$sysid=isset($_GET['sys'])?intval($_GET['sys']):1;

if(isset($_GET['kw'])) {
	$kw=daddslashes($_GET['kw']);
	$sql=" `url` LIKE '%{$kw}%'";
	$rownum='包含'.$_GET['kw'].'的共有';
	$sysid=$kw;
}
else {
	$sql=" sysid='{$sysid}'";
	$rownum='你总共建立了';
}

$gls=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE uid='{$uid}'");
if($row['wzjobnum']!=$gls)
	$DB->query("UPDATE ".DBQZ."_user SET wzjobnum= '$gls' WHERE userid = '$uid'");

$gls=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE uid='{$uid}' and{$sql}");

echo '<div class="alert alert-info">';
if($conf['server_wz']>1)echo '★ '.$sysname[$sysid].'号服务器任务列表★[<a href="index.php?mod=syslist&'.$link.'">返回服务器列表</a>]<br/>';
echo '★ '.$rownum.$gls.'个任务！';
if($isvip==0 && $rules[2]!=0 && $isadmin==0)echo '<br/>☆网址监控类任务扣币标准：<font color="red">每条任务每天收取 '.$rules[2].' '.$conf['coin_name'].'</font> 
[<a href="index.php?mod=shop&kind=1">开通VIP</a>]后可以免币';
echo '</div>';
echo '<div id="wzfunc">';
echo '
<span class="dropdown">
   <button href="#" class="btn btn-primary" data-toggle="dropdown" role="button">添加任务 <span class="caret"></span></button>
   <ul class="dropdown-menu" role="menu">
    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="wzjob_edit(\'add\',0,\''.$sysid.'\')">添加网址监控任务</a></li>
    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" onclick="wzjob_edit(\'bulk\',0,\''.$sysid.'\')">批量添加网址任务</a></li>
   </ul>
</span>
<span class="dropdown">
   <button href="#" class="btn btn-info" data-toggle="dropdown" role="button">任务操作 <span class="caret"></span></button>
   <ul class="dropdown-menu" role="menu">
    <li role="presentation"><a role="menuitem" href="index.php?mod=output&sys='.$sysid.$link.'">导出所有</a></li>
    <li role="presentation"><a role="menuitem" href="#" onclick="wzjob_edit(\'upload\',0,\''.$sysid.'\')">文件导入</a></li>
	<li role="presentation" class="divider"></li>
    <li role="presentation"><a role="menuitem" href="index.php?mod=set&my=qk&sys='.$sysid.$link.'">清空所有</a></li>
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
	$('#wzfunc').show();
	$('#list').html('<center><i class="fa fa-spinner fa-pulse"></i>正在加载...</center>');
	if($_GET['userid'])
		ajax.get("ajax.php?mod=list&act="+type+"&sysid=<?php echo $sysid ?>&page="+page+"&userid="+$_GET['userid'], "html", function(data) {
			$('#list').html(data);
		});
	else
		ajax.get("ajax.php?mod=list&act="+type+"&sysid=<?php echo $sysid ?>&page="+page, "html", function(data) {
			$('#list').html(data);
		});
}
function wzjob_edit(type,jobid,sysid,page) {
	jobid = jobid || 0;
	sysid = sysid || 1;
	page = page || 1;
	$('#wzfunc').hide();
	$('#list').html('<center><i class="fa fa-spinner fa-pulse"></i>请稍候...</center>');
	ajax.get("ajax.php?mod=wzjob&act=edit&type="+type+"&sysid="+sysid+"&jobid="+jobid+"&page="+page, "html", function(data) {
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
			showlist('wztask',page);
		}else{
			alert(arr.msg);
		}
	});
}
$(document).ready(function(){
showlist('wztask',1);
});
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