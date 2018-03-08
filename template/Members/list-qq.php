<?php
 /*
　*　QQ任务列表
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="QQ任务列表";
$time = time();
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li><a href="index.php?mod=qqlist"><i class="icon fa fa-qq"></i>ＱＱ管理</a></li>
<li class="active"><a href="#"><i class="icon fa fa-list-alt"></i>'.$_GET['qq'].'</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-12" role="main">';

if($islogin==1){

if($conf['active']!=0)phone_check();

if(OPEN_QQOL==0) {
	showmsg('当前站点未开启此功能。',2);
}
$qq=daddslashes($_GET['qq']);
$gls=$DB->count("SELECT count(*) from ".DBQZ."_qqjob WHERE qq='{$qq}' and uid='{$uid}'");

$qqrow=$DB->get_row("SELECT * FROM ".DBQZ."_qq WHERE qq='{$qq}' limit 1");
if($qqrow['uid']!=$uid && $isadmin!=1 && $isdeputy!=1)
{showmsg('你只能操作自己的QQ哦！',3);
}
if($qqlevelapi==5)$qqlevellink='qqdg.php?qq='.$qq.'&md5='.md5(SYS_KEY.$qq.$time).'&s='.$time;
else $qqlevellink='index.php?mod=qqlevel&qq='.$qq;
echo '
<table class="table table-bordered"> 
	<tbody>
	<tr>
		<td class="hidden-xs"><span class="btn btn-large btn-block btn-primary" style="line-height:35px;">QQ:'.$qq.'</span><a href="http://user.qzone.qq.com/'.$qq.'?" class="btn btn-large btn-block btn-warning" style="line-height:35px;" target="_blank" rel="noreferrer">进入QQ空间</a></td>
		<td align="center" valign="middle" class="panel panel-default" style="background:url(images/qqback.gif);background-size:cover;"><img src="//q1.qlogo.cn/g?b=qq&nk='.$qq.'&s=100&t='.date("Ymd").'" class="qqlogo"></td>
		<td>'.(OPEN_LEVE==1?'<a href="'.$qqlevellink.'" class="btn btn-large btn-block btn-info" style="line-height:35px;">QQ等级代挂</a>':'<span class="btn btn-large btn-block btn-info" style="line-height:35px;">QQ任务列表</span>').'<a href="index.php?mod=search&q='.$qq.'" class="btn btn-large btn-block btn-success" style="line-height:35px;" target="_blank">秒赞认证</a></td>
	</tr>
	</tbody>
</table>';
echo '<div id="qqfunc">';
if($isvip==0 && $rules[4]!=0 && $isadmin==0)echo '<div class="alert alert-info">QQ类任务扣币标准：<font color="red">每条任务每天收取 '.$rules[4].' '.$conf['coin_name'].'</font> 
[<a href="index.php?mod=shop&kind=1">开通VIP</a>]后可以免币</div>';
echo '
<span class="dropdown">
   <button href="#" class="btn btn-primary" data-toggle="dropdown" role="button">添加QQ挂机任务 <span class="caret"></span></button>
   <ul class="dropdown-menu" role="menu">
	<li role="presentation"><a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#qqjob" href="#" id="qqlist02">空间类任务</a></li>';
	if(OPEN_OTHE==1||OPEN_SHUA==1)echo '<li role="presentation"><a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#qqjob" href="#" id="qqlist03">互刷类任务</a></li>';
	echo '<li role="presentation"><a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#qqjob" href="#" id="qqlist04">普通签到类任务</a></li>
	<li role="presentation"><a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#qqjob" href="#" id="qqlist05">会员签到类任务</a></li>';
	if(OPEN_LEVE==1)echo '<li role="presentation"><a role="menuitem" href="'.$qqlevellink.'">等级代挂类任务</a></li>';
	if(OPEN_QZDS==1)echo '<li role="presentation"><a role="menuitem" href="index.php?mod=shua&qq='.$qq.'">ＱＱ代刷类任务</a></li>';
	echo '<li role="presentation"><a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#qqjob" href="#" id="qqlist01">其他类任务</a></li>';
	echo '<li role="presentation"><a role="menuitem" href="#" onclick="qqjob_edit(\''.$qq.'\',\'webqq\')">WEBQQ机器人</a></li>';
   echo '</ul>
</span>
<span class="dropdown">
   <button href="#" class="btn btn-success" data-toggle="dropdown" role="button">QQ小工具 <span class="caret"></span></button>
   <ul class="dropdown-menu" role="menu">
    <li role="presentation"><a role="menuitem" href="index.php?mod=dx&qq='.$qq.'" target="_blank">单向好友检测</a></li>
	<li role="presentation"><a role="menuitem" href="index.php?mod=mzjc&qq='.$qq.'" target="_blank">秒赞好友检测</a></li>
	<li role="presentation"><a role="menuitem" href="#" onclick="qqtool(\''.$qq.'\',\'qqz\')">刷圈圈赞99+</a></li>';
	if(OPEN_SHUAR==1)echo '<li role="presentation"><a role="menuitem" href="index.php?mod=reply&qq='.$qq.'" target="_blank">说说刷队形</a></li>';
	if(OPEN_SHUA==1)echo '<li role="presentation"><a role="menuitem" href="index.php?mod=sz&qq='.$qq.'" target="_blank">说说刷赞</a></li>
	<li role="presentation"><a role="menuitem" href="index.php?mod=rq&qq='.$qq.'" target="_blank">空间刷人气</a></li>';
	echo '<li role="presentation"><a role="menuitem" href="#" onclick="qqtool(\''.$qq.'\',\'ebook\')">领取图书VIP</a></li>
	<li role="presentation"><a role="menuitem" href="http://ptlogin2.qq.com/jump?uin='.$qq.'&skey='.$qqrow['skey'].'&u1=http://kf.qq.com/touch/qzone/qzone_status.html" target="_blank" rel="noreferrer">空间异常诊断</a></li>
	<li role="presentation"><a role="menuitem" href="http://ptlogin2.qq.com/jump?uin='.$qq.'&skey='.$qqrow['skey'].'&u1=http://kf.qq.com/qzone/remove_qzone.html" target="_blank" rel="noreferrer">解除禁言</a></li>
	<li role="presentation"><a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#qqjob" href="#" id="qqlist07">>>更多小工具</a></li>
   </ul>
</span>
<span class="dropdown">
   <button href="#" class="btn btn-info" data-toggle="dropdown" role="button">批量操作 <span class="caret"></span></button>
   <ul class="dropdown-menu" role="menu">
    <li role="presentation"><a role="menuitem" href="index.php?mod=set&my=qkqqrw&qq='.$qq.$link.'">清空所有</a></li>
	<li role="presentation"><a role="menuitem" href="#" onclick="job_edit(\'ztall\',\''.$qq.'\',\'qqjob\',\'1\')">一键暂停所有</a></li>
	<li role="presentation"><a role="menuitem" href="#" onclick="job_edit(\'kqall\',\''.$qq.'\',\'qqjob\',\'1\')">一键开启所有</a></li>
   </ul>
</span>
';
echo '</div>';
?>

<script>
function qqtool(qq,type) {
	if(type=='ebook')
		var url="qq/api/ebook.php?uin="+qq+"&skey=<?php echo urlencode($qqrow['skey']) ?>";
	else if(type=='qqz')
		var url="index.php?mod=qqz&qq="+qq;
	ajax.get(url, "json", function(arr) {
		alert(arr.msg);
	});
}
function showlist(type,page) {
	page = page || '1';
	$('#qqfunc').show();
	$('#list').html('<center><i class="fa fa-spinner fa-pulse"></i>正在加载...</center>');
	ajax.get("ajax.php?mod=list&act="+type+"&qq=<?php echo $qq ?>&page="+page, "html", function(data) {
		$('#list').html(data);
	});
}
function qqjob_edit(qq,type,jobid,page) {
	jobid = jobid || 0;
	page = page || 1;
	$('#qqjob').modal('hide');
	$('#qqfunc').hide();
	$('#list').html('<center><i class="fa fa-spinner fa-pulse"></i>请稍候...</center>');
	ajax.get("ajax.php?mod=qqjob&act=edit&qq="+qq+"&type="+type+"&jobid="+jobid+"&page="+page, "html", function(data) {
		$('#list').html(data);
	});
}
function qqjob_add(qq,type) {
	$('#qqjob').modal('hide');
	ajax.get("ajax.php?mod=qqjob&act=add&qq="+qq+"&type="+type, "json", function(arr) {
		if(arr.code==1){
			alert(arr.msg);
			showlist('qqtask',1);
		}else{
			alert(arr.msg);
		}
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
			showlist('qqtask',page);
		}else{
			alert(arr.msg);
		}
	});
}
$(document).ready(function(){
$("#qqlist01").click(function(){
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=1&qq=<?php echo $qq ?>",async:false});
  $("#myDiv").html(htmlobj.responseText);
});
$("#qqlist02").click(function(){
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=2&qq=<?php echo $qq ?>",async:false});
  $("#myDiv").html(htmlobj.responseText);
});
$("#qqlist03").click(function(){
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=3&qq=<?php echo $qq ?>",async:false});
  $("#myDiv").html(htmlobj.responseText);
});
$("#qqlist04").click(function(){
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=4&qq=<?php echo $qq ?>",async:false});
  $("#myDiv").html(htmlobj.responseText);
});
$("#qdlist").click(function(){
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=5",async:false});
  $("#myDiv").html(htmlobj.responseText);
});
$("#qqlist05").click(function(){
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=6&qq=<?php echo $qq ?>",async:false});
  $("#myDiv").html(htmlobj.responseText);
});
$("#qqlist06").click(function(){
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=7&qq=<?php echo $qq ?>",async:false});
  $("#myDiv").html(htmlobj.responseText);
});
$("#qqlist07").click(function(){
  htmlobj=$.ajax({url:"template/Ajax/display.php?list=11&qq=<?php echo $qq ?>",async:false});
  $("#myDiv").html(htmlobj.responseText);
});
showlist('qqtask',1);
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