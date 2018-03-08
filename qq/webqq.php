<?php
/*
 *WEBQQ机器人
*/
require 'qq.inc.php';

$qq=isset($_GET['qq']) ? $_GET['qq'] : null;
$skey=isset($_GET['skey']) ? $_GET['skey'] : null;
$superkey=isset($_GET['superkey']) ? $_GET['superkey'] : null;
$method=isset($_GET['method']) ? $_GET['method'] : 0;
$robot=isset($_GET['robot']) ? $_GET['robot'] : 1;
$content=isset($_GET['content']) ? $_GET['content'] : null;
$nick=isset($_GET['nick']) ? $_GET['nick'] : null;
$apikey=isset($_GET['apikey']) ? $_GET['apikey'] : null;
$apisecret=isset($_GET['apisecret']) ? $_GET['apisecret'] : null;
$cookie_file='./cookie/'.md5(RUN_KEY.$qq.RUN_KEY).'.txt';

if($qq && $skey && $superkey){}else{echo"<font color='red'>输入不完整!<a href='javascript:history.back();'>返回重新填写</a></font>";exit;}

$starttime=time();
$runtime=50;//每次运行持续时间(秒)

require_once 'webqq.class.php';
$qzone=new webqq($qq,$skey);
if(file_exists($cookie_file)){
	$qzone->cookie=file_get_contents($cookie_file);
	if($qzone->login2()){
		$process=true;
	}else{
		if($data=$qzone->login($superkey)){
			file_put_contents($cookie_file,$data);
			if($qzone->login2()){
				$process=true;
			}
		}else{
			sendsiderr($qq,$skey,'superkey');
		}
	}
}else{
	if($data=$qzone->login($superkey)){
		file_put_contents($cookie_file,$data);
		if($qzone->login2()){
			$process=true;
		}
	}else{
		sendsiderr($qq,$skey,'superkey');
	}
}
if($process){
	$qzone->online();
	if($robot==1)$content='robot';
	if(isset($_GET['runkey'])){
		while(time()-$starttime<$runtime){
			$result=$qzone->poll();
			if($result['retcode']==103)exit('请到 <a href="http://w.qq.com/" target="_blank">w.qq.com</a> 扫码登录一下，即可继续使用！扫码登录后可关闭页面');
			$qzone->process_message($result,$content,$method,$nick);
			usleep(100000);
		}
	}else{
		$result=$qzone->poll();
		if($result['retcode']==103)exit('请到 <a href="http://w.qq.com/" target="_blank">w.qq.com</a> 扫码登录一下，即可继续使用！扫码登录后可关闭页面');
		$qzone->process_message($result,$content,$method,$nick);
	}
}

//结果输出
foreach($qzone->msg as $result){
	echo $result.'<br/>';
}

?>