<?php
if(!defined('IN_CRONLITE'))exit();
$title="首页";
include_once(TEMPLATE_ROOT."head.php");

navi();

$gg=$conf['gg'];
if(!empty($gg))
echo'<div class="w h">公告栏</div><div class="box">'.$gg.'</div>';
showlogin();
echo '<div class="panel panel-primary">
<div class="panel-heading" style="background: #b2a800;"><h3 class="panel-title" align="center">运行日志:&nbsp&nbsp<a href="index.php?mod=all">查看详细>></a></h3></div><div class="panel-body" align="left"><p class="bg-danger" style="padding: 10px; font-size: 90%;" align="center">现在系统共有<font color="#ff0000">'.$zongs.'</font>条挂机任务</font></p>	<p class="bg-warning" style="padding: 10px; font-size: 90%;" align="center">系统累计运行了<font color="#ff0000">'.$info['times'].'</font>次</font></p><p class="bg-success" style="padding: 10px; font-size: 90%;" align="center">上次运行:<font color="#ff0000">'.$info['last'].'</font></p><p class="bg-info" style="padding: 10px; font-size: 90%;" align="center">当前时间:<font color="#ff0000">'.$date.'</font></p></div></div>';
//echo'<div class="panel panel-primary">
//<div class="panel-heading"><h3 class="panel-title">数据统计:</h3></div>';
//echo'<div class="panel-body">';
//include(ROOT.'includes/content/tongji.php');
//echo'</div></div>';

