<?php
if(!defined('IN_CRONLITE'))exit();

function showmsg($content = '未知的异常',$type = 4,$back = false,$die = false)
{
	global $conf,$cdnserver;
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
	global $link,$qq;
	echo '<hr/><a href="index.php?mod=list&qq='.$qq.$link.'">>> 进入添加任务</a><br/><a href="index.php?mod=qqlist'.$link.'"><< 返回我的QQ列表</a>';
} elseif ($back == 'addqq2') {
	global $link;
	echo '<hr/><a href="index.php?mod=qqlist'.$link.'"><< 返回我的QQ列表</a>';
} elseif ($back == 'shop') {
	echo '<hr/><a href="../index.php?mod=shop" pjax="no"><< 返回上一页</a>';
}
else
    echo '<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a>';

echo '</div>
    </div>';
include TEMPLATE_ROOT."foot.php";
exit;
}


function checkIfActive($string) {
	global $mod,$m,$already;
	if (($mod == $string || $m==$string) && !$already){
		$already=1;
		return 'active';
	}elseif ($string=='admin' && strexists($mod, 'admin') && !$already){
		$already=1;
		return 'active';
	}else{
		return null;
	}
}
?>