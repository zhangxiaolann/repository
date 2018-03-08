<?php
if(!defined('IN_CRONLITE'))exit();
$title="QQ挂机列表";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-qq"></i>ＱＱ列表</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-12" role="main">';

if($islogin==1){

if(isset($_GET["super"]) && $isadmin==1) {
$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE 1");
$gxsid=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE status!='1'");
} else {
$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE uid='{$uid}'");
if($row['qqnum']!=$gls)
	$DB->query("UPDATE ".DBQZ."_user SET qqnum= '$gls' WHERE userid = '$uid'");
$qqjobnum=$DB->count("SELECT count(*) from ".DBQZ."_qqjob WHERE uid='{$uid}'");
if($row['qqjobnum']!=$qqjobnum)
	$DB->query("UPDATE ".DBQZ."_user SET qqjobnum= '$gls' WHERE userid = '$uid'");

$gxsid=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE status2!='1' and uid='{$uid}'");
}
if(OPEN_QQOL==0) {
	showmsg('当前站点未开启此功能。',2);exit;
}
if($conf['active']!=0)phone_check();
echo '<div id="func">';
echo '<div class="alert alert-info">★你总共添加了 <font color="red">'.$gls.'</font> 个QQ账号！
<br/>你当前有 <font color="red">'.$gxsid.'</font> 个QQ的SKEY等待更新！<br/>
[<a href="index.php?mod=set&my=mail&'.$link.'">点此设置SID&Skey失效提醒邮箱</a>]</div>';
echo '
<a href="#" onclick="addqq(\'login\')" class="btn btn-success btn-lg"><i class="fa fa-plus"></i>&nbsp;添加QQ账号</a>&nbsp;
<span class="dropdown">
   <button href="#" class="btn btn-info" data-toggle="dropdown" role="button">批量操作 <span class="caret"></span></button>
   <ul class="dropdown-menu" role="menu">
	'.($isadmin==1?'<li role="presentation"><a role="menuitem" href="index.php?mod=import">批量导入</a></li>':null).'
    <li role="presentation"><a role="menuitem" href="index.php?mod=set&my=qkqq'.$link.'">清空所有</a></li>
   </ul>
   &nbsp;<button href="#" data-toggle="modal" data-target="#help" class="btn btn-default" id="help">帮助</button>
</span>
';
echo '</div>';

$pagesize=$conf['pagesize'];
if (!isset($_GET['page'])) {
	$page = 1;
	$pageu = $page - 1;
} else {
	$page = $_GET['page'];
	$pageu = ($page - 1) * $pagesize;
}

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
function addqq(act,qq) {
	if(act=='del') {
		if(!confirm('你确实要删除此QQ及此QQ下所有挂机任务吗？'))return false;
		ajax.get("ajax.php?mod=addqq&act="+act+"&qq="+qq, "json", function(arr) {
			if(arr.code==1){
				alert(arr.msg);
				showlist('qqlist',1);
			}else{
				alert(arr.msg);
			}
		});
		return true;
	}
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
	if($_GET['qq'])
		ajax.get("ajax.php?mod=list&act="+type+"&page="+page+"&super=1&qq="+$_GET['qq'], "html", function(data) {
			$('#list').html(data);
		});
	else if($_GET['super']==1)
		ajax.get("ajax.php?mod=list&act="+type+"&page="+page+"&super=1", "html", function(data) {
			$('#list').html(data);
		});
	else if($_GET['userid'])
		ajax.get("ajax.php?mod=list&act="+type+"&page="+page+"&userid="+$_GET['userid'], "html", function(data) {
			$('#list').html(data);
		});
	else
		ajax.get("ajax.php?mod=list&act="+type+"&page="+page, "html", function(data) {
			$('#list').html(data);
		});
}
$(document).ready(function(){
	$("#help").click(function(){
		htmlobj=$.ajax({url:"template/Ajax/display.php?list=9",async:false});
	$("#myDiv").html(htmlobj.responseText);
});
showlist('qqlist',1);
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