<?php
 /*
　* 网址监控
*/ 
if(!defined('IN_CRONLITE'))exit();
$title="网址监控";
$breadcrumb='<li><a href="index.php?mod=user"><i class="icon fa fa-home"></i>首页</a></li>
<li class="active"><a href="#"><i class="icon fa fa-list-alt"></i>网址监控</a></li>';
include TEMPLATE_ROOT."head.php";

echo '<div class="col-md-8 col-sm-10 col-xs-12 center-block" role="main">';

if($islogin==1){

$gls=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE uid='$uid'");
if($row['wzjobnum']==$gls){}else{
	$DB->query("UPDATE ".DBQZ."_user SET wzjobnum='$gls' WHERE userid='$uid'");}

if(OPEN_CRON==0) {
	showmsg('当前站点未开启此功能。',2);exit;
}
if($conf['active']!=0)phone_check();

echo '<div class="modal fade" align="left" id="search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">搜索任务</h4>
      </div>
      <div class="modal-body">
      <form action="index.php" method="GET">
<input type="hidden" name="mod" value="list-wz">
<input type="text" class="form-control" name="kw" placeholder="请输入关键字"><br/>
<input type="submit" class="btn btn-primary btn-block" value="搜索"></form>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>';
echo '<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title" align="center">网址监控任务控制面板</h3></div>';
echo '<div class="panel-body">请从以下'.$conf['server_wz'].'个系统中选择一个总任务数较少的来添加你的网址监控任务。<br/>本系统有以下功能：自定义运行时间,自定义使用代理,自定义代理ip及端口号,自定义POST模拟,自定义POST数据,自定义COOKIE,自定义来源地址,自定义模拟浏览器。<br/><br/>';
echo '
<div class="alert alert-info">你总共建立了'.$gls.'条任务 [<a href="index.php?mod=output">导出任务</a>]&nbsp;[<a href="#" data-toggle="modal" data-target="#search" id="search">搜索</a>]</div>';

$show=explode('|',$conf['show']);

echo '<div class="table-responsive"><table class="table table-hover"><thead><tr>';
echo '<th>任务系统</th><th>总任务数</th><th>你的任务</th><th>执行频率</th></thead><tbody>';

for($i=1;$i<=$conf['server_wz'];$i++) {
	$addstr='';
	$all_sys=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE sysid='$i'");
	$my_sys=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE sysid='$i' and uid='$uid'");
	echo '<tr><td><a href="index.php?mod=list-wz&sys='.$i.$link.'">【系统'.$sysname[$i].'】</a></td><td>'.$all_sys.'/'.$conf['max'].'</td><td>'.$my_sys.'</td><td><font color=#0000FF>'.$show[($i-1)].'</font></td></tr>';
}
$zongs=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE 1");
echo '</tbody></table></div></div></div>';
echo '<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">运行日志:&nbsp&nbsp<a href="index.php?mod=all">详细>></a></h3></div><div class="panel-body">系统共有<font color="#ff0000">'.$zongs.'</font>条任务<br>系统累计运行了<font color="#ff0000">'.$info['times'].'</font>次<br>上次运行:<font color="#ff0000">'.$info['last'].'</font><br>当前时间:<font color="#ff0000">'.$date.'</font></div>';
if(function_exists("sys_getloadavg")){
echo '<div class="panel-heading"><h3 class="panel-title">系统负载:</h3></div>';
$f=sys_getloadavg();
echo '<div class="panel-body">';
echo "1min:{$f[0]}";
echo "|5min:{$f[1]}";
echo "|15min:{$f[2]}";
echo '</div>';}
echo '</div>';
  }
else{
showmsg('登录失败，可能是密码错误或者身份失效了，请<a href="index.php?mod=login">重新登录</a>！',3);}

include TEMPLATE_ROOT."foot.php";
?>