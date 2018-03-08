<?php
if(!defined('IN_CRONLITE'))exit();

$referer=parse_url($_SERVER['HTTP_REFERER']);
$referer=$referer['host'];

if($http_host!=$referer)exit('CSRF Security Error');

@header('Content-Type: text/html; charset=UTF-8');

function showmsg($content = '未知的异常',$type = 4,$back = false,$die = false)
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

global $mod;
if ($mod == 'qqjob') {
	echo '<hr/><a href="#" onclick="showlist(\'qqtask\',1)""><< 返回我的任务列表</a>';
} elseif ($mod == 'signjob') {
	echo '<hr/><a href="#" onclick="showlist(\'signtask\',1)""><< 返回我的任务列表</a>';
} elseif ($mod == 'wzjob') {
	echo '<hr/><a href="#" onclick="showlist(\'wztask\',1)""><< 返回我的任务列表</a>';
} elseif ($mod == 'addqq') {
	echo '<hr/><a href="#" onclick="showlist(\'qqlist\',1)""><< 返回我的QQ列表</a>';
}

echo '</div>
    </div>';
include ROOT."template/Home/foot.php";
exit;
}
?>