<?php
/*
 *积分商城签到
*/
require 'qq.inc.php';

$qq=isset($_GET['qq']) ? $_GET['qq'] : null;
$skey=isset($_GET['skey']) ? $_GET['skey'] : null;
if($qq && $skey){}else{echo"<font color='red'>输入不完整!<a href='javascript:history.back();'>返回重新填写</a></font>";exit;}
include '../includes/authcode.php';

$data=curl_get('http://api.cccyun.cc/api/jfsc.php?uin='.$qq.'&skey='.$skey.'&url='.$_SERVER['HTTP_HOST'].'&authcode='.$authcode);
echo $data;

//SID失效通知
if(strpos($data,'SKEY')!==false){
	sendsiderr($qq,$skey,'skey');
}
?>