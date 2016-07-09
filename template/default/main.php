<?php
if(!defined('IN_CRONLITE'))exit();
function navi($width = '480px')
{
	global $islogin;
echo <<<HTML
<div class="col-md-9" role="main" style="margin: 0 auto;max-width:{$width};">
<div class="panel panel-primary">
	<div class="panel-body" style="text-align: center;">
		<img src="images/logo.png">
	</div>
</div>
HTML;
if($islogin==1)
echo '<ul class="nav nav-pills visible-xs visible-sm">
  
  
  <li role="presentation" class="'.checkIfActive("user").'"><a href="index.php?mod=index">网站首页</a></li>
  
  <li role="presentation" class="'.checkIfActive("user").'"><a href="index.php?mod=user">网址监控</a></li>
  
</ul>';
}
function showmsg($content = '未知的异常',$type = 4,$back = false)
{
switch($type)
{
case 1:
	$panel="success";
break;
case 2:
	$panel="info";
break;
case 3:
	$panel="warning";
break;
case 4:
	$panel="danger";
break;
}

echo '<div class="panel panel-'.$panel.'">
      <div class="panel-heading">
        <h3 class="panel-title">提示信息</h3>
        </div>
        <div class="panel-body">';
echo $content;

if ($back == 'rw') {
	global $sysid,$link;
	echo '<hr/><a href="'.$_SERVER['HTTP_REFERER'].'"><< 返回我的任务列表</a>';
} elseif ($back == 'addrw') {
	global $sysid,$link;
	echo '<hr/><a href="'.$_SERVER['HTTP_REFERER'].'">>> 继续添加</a>';
	echo '<br/><a href="index.php?mod=list&sys='.$sysid.$link.'"><< 返回我的任务列表</a>';
} elseif ($back == 'addqqrw') {
	global $proxy,$link;
	echo '<hr/><a href="index.php?mod=list&m=qq&qq='.$proxy.$link.'"><< 返回我的任务列表</a>';
} elseif ($back == 'addqdrw') {
	global $link;
	echo '<hr/><a href="index.php?mod=list&m=sign&sign=1'.$link.'"><< 返回我的任务列表</a>';
} elseif ($back == 'addqq') {
	global $qq,$link;
	echo '<hr/><a href="index.php?mod=list&qq='.$qq.$link.'">>> 进入添加任务</a><br/><a href="index.php?mod=qqlist'.$link.'"><< 返回我的QQ列表</a>';
} elseif ($back == 'addqq2') {
	global $link;
	echo '<hr/><a href="index.php?mod=qqlist'.$link.'"><< 返回我的QQ列表</a>';
}
else
    echo '<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a>';

echo '</div>
    </div>';
}

function showlogin()
{
global $islogin,$isadmin,$row;
if($islogin==1)
{
echo '<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title" align="center">用户中心</h3></div>
<table class="table table-bordered">
<tbody>';
echo '<tr height=50>
<td><b>用户名/UID</b></td>
	<td align="center">'.$row['user'].'/['.$row['userid'].']<br/>('.usergroup().')</td>
	<td align="center"><a href="index.php?mod=userinfo"class="btn btn-block btn-info">用户资料</a></td>
</tr>';
if(OPEN_CRON==1)echo '<tr height=50>
	<td><b>任务数量</b></td>
	<td align="center">'.$row['num'].'个</td>
	<td align="center"><a href="index.php?mod=user" class="btn btn-block btn-primary">任务管理</a></td>
</tr>';

echo '<tr height=50>
	<td><b>用户操作</b></td>
	<td align="center"><a href="index.php?mod=user" class="btn btn-block btn-warning">网站监控</a></td>
	<td align="center"><a href="./?my=loginout" class="btn btn-block btn-danger">安全退出</a></td>
</tr>';
echo '</tbody>
</table>
</div>';
}
else
{
?>
<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">登录你的账号:</h3></div>
<div class="panel-body">
<form action="?" method="get">
<input type="hidden" name="my" value="login">
<div class="input-group">
<span class="input-group-addon">账号</span>
<input type="text" class="form-control" name="user" value="">
</div><br/>
<div class="input-group">
<span class="input-group-addon">密码</span>
<input type="password" class="form-control" name="pass" value="">
</div><br/>
<div class="login-button">
<input type="checkbox" name="ctime" id="ctime" checked="checked" value="2592000" >&nbsp;<label for="ctime">下次自动登录</label><br/><br/>
<button type="submit" class="btn btn-primary btn-block">马上登录</button><br/></form>
<a href="index.php?mod=reg" class="btn btn-success btn-block">注册用户</a>
</div>
</div>
</div>
<?php
}
}

function checkIfActive($string) {
	global $mod,$m,$already;
	if (($mod == $string || $m==$string) && !$already){
		$already=1;
		return 'active';
	}elseif ($string=='admin' && strexists($mod, 'admin') && !$already){
		$already=1;
		return 'active';
	}else
		return null;
}
?>