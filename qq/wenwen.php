<?php
/*
 *群问问签到
*/

require 'qq.inc.php';

$qq=isset($_GET['qq']) ? $_GET['qq'] : null;
$skey=isset($_GET['skey']) ? $_GET['skey'] : null;
$pskey=isset($_GET['pskey']) ? $_GET['pskey'] : null;
$superkey=isset($_GET['superkey']) ? $_GET['superkey'] : null;
$method=isset($_GET['method']) ? $_GET['method'] : 0;
$forbid=isset($_GET['forbid']) ? $_GET['forbid'] : null;

if($qq && $skey && $superkey){}else{echo"<font color='red'>输入不完整!<a href='javascript:history.back();'>返回重新填写</a></font>";exit;}

$forbid=explode("|",$forbid);
require_once 'qqsign.class.php';
$qzone=new qqsign($qq,$sid,$skey,$pskey);
$qzone->wenwen($superkey,$forbid,$method);

//结果输出
if($isdisplay){
	foreach($qzone->msg as $result){
		echo $result.'<br/>';
	}
}

?>