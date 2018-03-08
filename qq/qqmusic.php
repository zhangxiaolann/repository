<?php
/*
 *QQ音乐等级加速
*/
require 'qq.inc.php';

$qq=isset($_GET['qq']) ? $_GET['qq'] : null;
if($qq){}else{echo"<font color='red'>输入不完整!<a href='javascript:history.back();'>返回重新填写</a></font>";exit;}
include '../includes/authcode.php';

$data=curl_get('http://api.cccyun.cc/api/qqmusic.php?uin='.$qq.'&url='.$_SERVER['HTTP_HOST'].'&authcode='.$authcode);
echo $data;

?>