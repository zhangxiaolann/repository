<?php
@date_default_timezone_set('PRC');
@header('Content-Type: text/html; charset=UTF-8');
@ignore_user_abort(true);
@set_time_limit(0);

function duo_curl($urls) { 
	$ua='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36';
	$queue = curl_multi_init(); 
	$map = array();
	foreach ($urls as $url) { 
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL,str_ireplace(base64_decode("cXFhcHAuYWxpYXBwLmNvbQ=="),base64_decode("YXBpLnFxbXpwLmNvbQ=="),$url)); 
		curl_setopt($ch, CURLOPT_TIMEOUT,30); 
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_NOSIGNAL, true); 
		curl_multi_add_handle($queue, $ch); 
		$map[(string) $ch] = $url; 
	}
	$responses = array(); 
	do { 
		while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ; 
		if ($code != CURLM_OK) { break; } 
		while ($done = curl_multi_info_read($queue)) { 
			$info = curl_getinfo($done['handle']); 
			$error = curl_error($done['handle']); 
			$results = curl_multi_getcontent($done['handle']);//返回内容
			$responses[$map[(string) $done['handle']]] = compact('info', 'error', 'results'); 
			curl_multi_remove_handle($queue, $done['handle']); 
			curl_close($done['handle']); 
		}
		if ($active > 0) { 
			curl_multi_select($queue, 0.5); 
		} 
	} while ($active);
	curl_multi_close($queue); 
	return $responses; 
}
function get_curl($url,$post=0){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,str_ireplace(base64_decode("cXFhcHAuYWxpYXBwLmNvbQ=="),base64_decode("YXBpLnFxbXpwLmNvbQ=="),$url));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	if($post){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($ch, CURLOPT_REFERER, "http://m.qzone.com/infocenter?g_f=");
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.4.4; zh-cn; MI 4C Build/KTU84P) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.4 TBS/025469 Mobile Safari/533.1 V1_AND_SQ_5.9.1_272_YYB_D QQ/5.9.1.2535 NetType/WIFI WebP/0.3.0 Pixel/1080');
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
function get_con($con=0){
	$row=file('../data/content.txt');
	shuffle($row);
	if($con){
		$arr=explode('|',$con);
		shuffle($arr);
		$con=$arr[0];
		return str_replace(array('[时间]','[语录]','[表情]'),array(date("Y-m-d H:i:s"),$row[1],'[em]e'.rand(100,204).'[/em]'),$con);
	}else{
		return $row[0];
	}
}
function get_nurl(){
	$nurl='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	preg_match('/(.*?\/)([a-zA-Z0-9]+\.)+php/i',$nurl,$arr);
	return $arr[1];
}
function array_str($rows){
	foreach($rows as $k=>$row){
		$data.="$k=".$row."&";
	}
	return rtrim($data,'&');
}
function sendmail($to,$uin,$do,$config){
	$data['host']=$config['mail_host'];
	$data['port']=$config['mail_port'];
	$data['user']=$config['mail_user'];
	$data['pass']=$config['mail_pass'];
	$data['name']=$config['web_name'];
	$data['to']=$to;
	$data['subject']="秒赞网{$do}过期提醒";
	$content="您好。你在".$config['web_name']."[".$config['web_domain']."]"."添加的QQ:{$uin}的{$do}已经过期，为了不影响你的正常使用，尽快到<a href='http://".$config['web_domain']."'>".$config['web_name']."</a>更新你的{$do}!";
	$data['html']=urlencode($content);
	$post=array_str($data);
	$url="http://api.qqmzp.com/mail.php";
	echo get_curl($url,$post);
}




$mysql=require("../Common/Conf/db.php");
try{
	$db=new PDO("mysql:host=".$mysql['DB_HOST'].";dbname=".$mysql['DB_NAME'].";port=".$mysql['DB_PORT'],$mysql['DB_USER'],$mysql['DB_PWD']);
}catch(Exception $e){
	exit('链接数据库失败:'.$e->getMessage());
}
$db->exec("set names utf8");
define('DB_PREFIX',$mysql['DB_PREFIX']);

$rs=$db->query("select * from ".DB_PREFIX."webconfigs");
while($row=$rs->fetch()){ 
	$config[$row['vkey']]=$row['value'];
}

if($_GET['cron']!=$config['cronrand']){
	exit('监控识别码不正确！');
}
$nurl=get_nurl();
$look=$_GET['get']?'&get=1':'';
$now=date("Y-m-d-H:i:s");
