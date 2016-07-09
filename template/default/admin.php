<?php
 /*
　*　后台管理文件
*/
if(!defined('IN_CRONLITE'))exit();

if(isset($_GET['type'])){
	if($_GET['type']=='user')
		exit('<script>window.location.href="index.php?mod=admin-user&kw='.$_GET['kw'].'";</script>');
	elseif($_GET['type']=='job')
		exit('<script>window.location.href="index.php?mod=admin-job&kw='.$_GET['kw'].'";</script>');
	elseif($_GET['type']=='qq')
		exit('<script>window.location.href="index.php?mod=qqlist&super=1&qq='.$_GET['kw'].'";</script>');
}

$title="后台管理";
include_once(TEMPLATE_ROOT."head.php");

navi();

if(defined("SAE_ACCESSKEY"))$host = SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT;

if ($isadmin==1)
{
echo '<div class="panel panel-primary">';
echo '<div class="panel-heading"><h3 class="panel-title">后台管理</h3></div><div class="panel-body">';

echo '</div>';

echo '<div class="panel-heading"><h3 class="panel-title" align="center">基本操作</h3></div><div class="panel-body">';
echo '<a href="index.php?mod=admin-job" class="btn btn-success btn-block">任务数据管理</a>';
echo '<p align="center"><a href="index.php?mod=admin-job&sys=1" class="btn btn-default btn-sm">系统①</a>&nbsp;<a href="index.php?mod=admin-job&sys=2" class="btn btn-default btn-sm">系统②</a>&nbsp;<a href="index.php?mod=admin-job&sys=3" class="btn btn-default btn-sm">系统③</a>&nbsp;<a href="index.php?mod=admin-job&sys=4" class="btn btn-default btn-sm">系统④</a><br><a href="index.php?mod=admin-job&sys=5" class="btn btn-default btn-sm">系统⑤</a>&nbsp;<a href="index.php?mod=admin-job&sys=6" class="btn btn-default btn-sm">系统⑥</a>&nbsp;<a href="index.php?mod=admin-job&sys=7" class="btn btn-default btn-sm">系统⑦</a>&nbsp;<a href="index.php?mod=admin-job&sys=8" class="btn btn-default btn-sm">系统⑧</a></p><br>';
echo '</div><div class="panel-heading"><h3 class="panel-title" align="center">系统设置</h3></div><div class="panel-body">';
echo '<a href="index.php?mod=admin-set&my=set_config" class="btn btn-default btn-block">网站信息配置</a>

<a href="index.php?mod=admin-set&my=set_rw" class="btn btn-default btn-block">任务运行配置</a>
<a href="index.php?mod=admin-set&my=help" class="btn btn-info btn-block">任务监控说明</a>

<a href="index.php?mod=admin-set&my=set_gg" class="btn btn-default btn-block">广告与公告配置</a></div>';

echo '<div class="panel-heading"><h3 class="panel-title" align="center">外观设置</h3></div><div class="panel-body">';
echo '<a href="index.php?mod=admin-set&my=set_css" class="btn btn-default btn-block">更改系统皮肤 <font color="red">[NEW]</font></a>
<a href="index.php?mod=admin-set&my=logo" class="btn btn-default btn-block">更改系统LOGO</a>
<a href="index.php?mod=admin-set&my=bj2" class="btn btn-default btn-block">更改背景图片</a>
</div>';

echo '<div class="panel-heading"><h3 class="panel-title" align="center">清空相关</h3></div><div class="panel-body">';
echo '

<a href="index.php?mod=admin-clear&my=users" class="btn btn-default btn-block">清空无挂机用户</a>
<a href="index.php?mod=admin-clear&my=jobs" class="btn btn-default btn-block">清空全部挂机任务</a>
<a href="index.php?mod=admin-clear&my=sysall" class="btn btn-default btn-block">清空全站所有数据</a>
</div>';



echo '<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">运行日志:&nbsp&nbsp<a href="index.php?mod=all">详细>></a></h3></div><div class="panel-body">系统共有<font color="#ff0000">'.$zongs.'</font>条任务<br>共有<font color="#ff0000">'.$qqs.'</font>个QQ正在挂机<br>系统累计运行了<font color="#ff0000">'.$info['times'].'</font>次<br>上次运行:<font color="#ff0000">'.$info['last'].'</font><br>当前时间:<font color="#ff0000">'.$date.'</font></div>';
if(function_exists("sys_getloadavg")){
echo'<div class="panel-heading"><h3 class="panel-title">系统负载:</h3></div>';
$f=sys_getloadavg();
echo'<div class="panel-body">';
echo"1min:{$f[0]}";
echo"|5min:{$f[1]}";
echo"|15min:{$f[2]}";
echo'</div>';}
echo'<div class="panel-heading"><h3 class="panel-title">数据统计:</h3></div>';
echo'<div class="panel-body">';
echo'系统共有'.$users.'个用户<br/>';
include(ROOT.'includes/content/tongji.php');
echo'</div></div>';
}
else
{
showmsg('后台管理登录失败。请以管理员身份 <a href="index.php?mod=login">重新登录</a>！',3);
}
echo'<div class="panel panel-primary"><div class="panel-body" style="text-align: center;">';
echo date("Y年m月d日 H:i:s");
include(ROOT.'includes/foot.php');
echo'</div></div>';
if($conf['sjyl']==1)
{$txt=file(ROOT.'includes/content/content.db');
shuffle($txt);
echo"$txt[0]";}
echo'</div></div></div></body></html>';
?>