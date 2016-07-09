<?php
//挂机统计文件
if(!defined('IN_CRONLITE'))exit();
$title="系统数据统计";
include_once(TEMPLATE_ROOT."head.php");

navi();

echo'<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">挂机统计</h3></div>';
echo'<div class="panel-body box">本站共有<font color=red>'.$users.'</font>位用户<br>';
echo'系统共有<font color=red>'.$zongs.'</font>条任务<br>';
for($i=1;$i<=$conf['sysnum'];$i++) {
	$all_sys=$DB->count("SELECT count(*) from wjob_job WHERE sysid='$i'");
	$info_sys=$DB->get_row("SELECT last from wjob_info WHERE sysid='$i' limit 1");
	echo'[系统'.$sysname[$i].']有<font color=red>'.$all_sys.'</font>条任务<br>';
	echo'上次运行:'.$info_sys['last'].'<br>';
}
echo'<hr>系统累计运行了<font color=red>'.$info['times'].'</font>次.<br>';
echo'上次运行:'.$info['last'].'<br>';
echo'当前时间:'.$date.'</div></div>';

//注：只有Linux主机才支持显示负载。
if(function_exists("sys_getloadavg")){
echo'<div class="panel panel-primary">
<div class="panel-heading w h"><h3 class="panel-title">系统负载:</h3></div>';
$f=sys_getloadavg();
echo'<div class="panel-body box">';
echo"1min:{$f[0]}";
echo"|5min:{$f[1]}";
echo"|15min:{$f[2]}";
echo'</div></div>';}

echo'<div class="panel panel-primary"><div class="panel-body box" style="text-align: center;">';
include(ROOT.'includes/foot.php');
echo'</div>';
if($conf['sjyl']==1)
{$txt=file(ROOT.'includes/content/content.db');
shuffle($txt);
echo"$txt[0]";}
echo'</div>
</div>
</div></body></html>';
?>