<?php
/*
 *群签到
*/

require 'qq.inc.php';

$qq=isset($_GET['qq']) ? $_GET['qq'] : null;
$skey=isset($_GET['skey']) ? $_GET['skey'] : null;
$pskey=isset($_GET['pskey']) ? $_GET['pskey'] : null;
$template=isset($_GET['template']) ? $_GET['template'] : 2;
$content=isset($_GET['content']) ? $_GET['content'] : null;
$forbid=isset($_GET['forbid']) ? $_GET['forbid'] : null;
$poi=isset($_GET['poi']) ? $_GET['poi'] : null;

if($qq && $skey && $pskey){}else{echo"<font color='red'>输入不完整!<a href='javascript:history.back();'>返回重新填写</a></font>";exit;}

$content = $content?$content:'签到';

$forbid=explode("|",$forbid);
require_once 'qqsign.class.php';
$qzone=new qqsign($qq,$sid,$skey,$pskey);
$qzone->qunqd($forbid,$poi,$template,$content);

//结果输出
if($isdisplay){
	foreach($qzone->msg as $result){
		echo $result.'<br/>';
	}
}

//SKEY失效通知
if($qzone->skeyzt){
	sendsiderr($qq,$skey,'skey');
}
?>